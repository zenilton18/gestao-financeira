<?php

namespace App\Http\Controllers;

use App\Models\Conta; // Certifique-se de que seu Model se chama Conta
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;
        $hoje = Carbon::today()->toDateString();

        // 1. Métricas do Mês
        $totalRecebidoMensal = Conta::where('tipo', 'receber')
            ->where('status', 'pago')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');

        $totalPagoMensal = Conta::where('tipo', 'pagar')
            ->where('status', 'pago')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');

        $saldoAtual = $totalRecebidoMensal - $totalPagoMensal;

        $totalPendenteMensal = Conta::where('status', 'pendente')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');

        // 2. Movimentações do Dia
        $entradasHoje = Conta::where('tipo', 'receber')
            ->where('status', 'pago')
            ->whereDate('data_vencimento', $hoje)
            ->sum('valor');

        $saidasHoje = Conta::where('tipo', 'pagar')
            ->where('status', 'pago')
            ->whereDate('data_vencimento', $hoje)
            ->sum('valor');

        // 3. Origem de Entradas
        $faturamentoOrigem = [
            'corte_barba'    => Conta::where('tipo', 'receber')->where('centro_custo', 'corte_barba')->where('status', 'pago')->sum('valor'),
            'venda_produtos' => Conta::where('tipo', 'receber')->where('centro_custo', 'venda_produtos')->where('status', 'pago')->sum('valor'),
            'combos_pacotes' => Conta::where('tipo', 'receber')->where('centro_custo', 'combos_pacotes')->where('status', 'pago')->sum('valor'),
            'outros'         => Conta::where('tipo', 'receber')->where('centro_custo', 'outros')->where('status', 'pago')->sum('valor'),
        ];

        // 4. Lista dos Últimos Lançamentos
        $ultimosLancamentos = Conta::orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'saldoAtual',
            'totalRecebidoMensal',
            'totalPagoMensal',
            'totalPendenteMensal',
            'entradasHoje',
            'saidasHoje',
            'faturamentoOrigem',
            'ultimosLancamentos'
        ));
    }
}