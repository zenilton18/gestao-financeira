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

            'centro_custo'    => [
                'nullable',
                'required_if:tipo,receber',
                'in:corte,barba,f1,outros',
            ],

            'descricao'       => 'nullable|string|max:255',

            'valor_total'     => 'required|numeric|min:0.01',

            'data_vencimento' => 'required|date',

            'status'          => 'required|in:pago,pendente',

            'data_pagamento'  => 'nullable|date',

            'parcelas'        => 'required|integer|min:1|max:120',

            'intervalo'       => 'required|in:mensal,30_dias,quinzenal,semanal,diario',
        ]);

        /*
         * Se o lançamento estiver pendente,
         * não devemos considerar data de pagamento.
         */
        if ($dadosValidados['status'] !== 'pago') {
            $dadosValidados['data_pagamento'] = null;
        }

        DB::transaction(function () use ($dadosValidados) {

            $totalParcelas = (int) $dadosValidados['parcelas'];

            $valorTotal = (float) $dadosValidados['valor_total'];

            /*
             * Divide o valor pelas parcelas mantendo
             * os centavos corretamente.
             */
            $valorBase = floor(
                ($valorTotal / $totalParcelas) * 100
            ) / 100;

            $resto = round(
                $valorTotal - ($valorBase * $totalParcelas),
                2
            );

            /*
             * Todas as parcelas pertencem ao mesmo grupo.
             */
            $grupoId = (string) Str::uuid();

            $primeiroVencimento = Carbon::parse(
                $dadosValidados['data_vencimento']
            );

            for ($i = 1; $i <= $totalParcelas; $i++) {

                /*
                 * Data da parcela atual.
                 */
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

                            $dataVencimento->addDays(
                                30 * ($i - 1)
                            );

                            break;

                        case 'quinzenal':

                            $dataVencimento->addDays(
                                15 * ($i - 1)
                            );

                            break;

                        case 'semanal':

                            $dataVencimento->addDays(
                                7 * ($i - 1)
                            );

                            break;
                    }
                }

                /*
                 * O restante dos centavos fica na primeira parcela.
                 */
                $valorParcela = ($i === 1)
                    ? ($valorBase + $resto)
                    : $valorBase;

                Conta::create([
                    'tipo' => $dadosValidados['tipo'],

                    /*
                     * Centro de custo só existe para RECEBIDO.
                     */
                    'centro_custo' =>
                        $dadosValidados['tipo'] === 'receber'
                            ? $dadosValidados['centro_custo']
                            : null,

                    'produto_id' => null,

                    'quantidade' => 1,

                    'descricao' =>
                        $dadosValidados['descricao'] ?? '',

                    'valor' => $valorParcela,

                    'data_vencimento' => $dataVencimento,

                    /*
                     * Data de pagamento somente quando
                     * o lançamento estiver pago.
                     */
                    'data_pagamento' =>
                        $dadosValidados['status'] === 'pago'
                            ? (
                                $dadosValidados['data_pagamento']
                                ?? now()->format('Y-m-d')
                            )
                            : null,

                    'status' => $dadosValidados['status'],

                    'numero_parcela' => $i,

                    'total_parcelas' => $totalParcelas,

                    'grupo_id' => $grupoId,
                ]);
            }
        });

        return redirect()
            ->route('contas.create')
            ->with(
                'sucesso',
                'Lançamento registrado com sucesso!'
            );
    }

    public function darBaixa(Request $request, $id)
    {
        $request->validate([
            'data_pagamento' => 'required|date',
        ]);

        $conta = Conta::findOrFail($id);

        $conta->update([
            'status' => 'pago',
            'data_pagamento' => $request->data_pagamento,
        ]);

        return redirect()
            ->back()
            ->with(
                'sucesso',
                'Lançamento marcado como pago!'
            );
    }
}