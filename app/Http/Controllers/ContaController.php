<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ContaController extends Controller
{
    public function create()
    {
        return view('financeiro.cadastro');
    }

    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'tipo'            => 'required|in:pagar,receber',
            'centro_custo'    => 'nullable|required_if:tipo,receber|in:corte_barba,venda_produtos,combos_pacotes,outros',
            'produto_id'      => 'nullable|required_if:centro_custo,venda_produtos|string',
            'quantidade'      => 'nullable|required_if:centro_custo,venda_produtos|integer|min:1',
            'descricao'       => 'nullable|string|max:255',
            'valor_total'     => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'status'          => 'nullable|in:pago,pendente',
            'data_pagamento'  => 'nullable|date',
            'parcelas'        => 'required|integer|min:1|max:120',
            'intervalo'       => 'required|in:mensal,30_dias,quinzenal,semanal,diario',
        ]);

        DB::transaction(function () use ($request, $dadosValidados) {
            $totalParcelas = (int) $dadosValidados['parcelas'];
            $valorTotal = (float) $dadosValidados['valor_total'];

            $valorBase = floor(($valorTotal / $totalParcelas) * 100) / 100;
            $resto = round($valorTotal - ($valorBase * $totalParcelas), 2);

            $grupoId = (string) Str::uuid();
            $primeiroVencimento = Carbon::parse($dadosValidados['data_vencimento']);

            for ($i = 1; $i <= $totalParcelas; $i++) {
                $dataVencimento = $primeiroVencimento->clone();

                if ($i > 1) {
                    switch ($dadosValidados['intervalo']) {
                        case 'diario':
                            $dataVencimento->addDays($i - 1);
                            break;
                        case 'mensal':
                            $dataVencimento->addMonthsNoOverflow($i - 1);
                            break;
                        case '30_dias':
                            $dataVencimento->addDays(30 * ($i - 1));
                            break;
                        case 'quinzenal':
                            $dataVencimento->addDays(15 * ($i - 1));
                            break;
                        case 'semanal':
                            $dataVencimento->addDays(7 * ($i - 1));
                            break;
                    }
                }

                $valorParcela = ($i === 1) ? ($valorBase + $resto) : $valorBase;

                // dd($dadosValidados);

                Conta::create([
                    'tipo'            => $dadosValidados['tipo'],
                    'centro_custo'    => $dadosValidados['tipo'] === 'receber' ? $dadosValidados['centro_custo'] : null,
                    'produto_id'      => $dadosValidados['centro_custo'] === 'venda_produtos' ? $dadosValidados['produto_id'] : null,
                    'quantidade'      => $dadosValidados['centro_custo'] === 'venda_produtos' ? ($dadosValidados['quantidade'] ?? 1) : 1,
                    'descricao'       => $dadosValidados['descricao'] ?? '',
                    'valor'           => $valorParcela,
                    'data_vencimento' => $dataVencimento,
                    'data_pagamento'  => ($request->status === 'pago') ? ($request->data_pagamento ?? date('Y-m-d')) : null,
                    'status'          => $request->status ?? 'pendente',
                    'numero_parcela'  => $i,
                    'total_parcelas'  => $totalParcelas,
                    'grupo_id'        => $grupoId,
                ]);
            }
        });

        return redirect()->route('contas.create')->with('sucesso', 'Lançamento registrado com sucesso!');
    }

    public function darBaixa(Request $request, $id)
    {
        $request->validate([
            'data_pagamento' => 'required|date',
        ]);

        $conta = Conta::findOrFail($id);
        $conta->update([
            'status'         => 'pago',
            'data_pagamento' => $request->data_pagamento,
        ]);

        return redirect()->back()->with('sucesso', 'Lançamento marcado como pago!');
    }
}