<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;
        $hoje = Carbon::today()->toDateString();

        // 1. Métricas do Mês (Apenas Operacional Financeiro - Exclui Venda de Produtos)
        $totalRecebidoMensal = Conta::apenasFinanceiro()
            ->where('tipo', 'receber')
            ->where('status', 'pago')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');

        $totalPagoMensal = Conta::apenasFinanceiro()
            ->where('tipo', 'pagar')
            ->where('status', 'pago')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');

        $saldoAtual = $totalRecebidoMensal - $totalPagoMensal;

        $totalPendenteMensal = Conta::apenasFinanceiro()
            ->where('status', 'pendente')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');

        // 2. Movimentações do Dia (Exclui Venda de Produtos)
        $entradasHoje = Conta::apenasFinanceiro()
            ->where('tipo', 'receber')
            ->where('status', 'pago')
            ->whereDate('data_vencimento', $hoje)
            ->sum('valor');

        $saidasHoje = Conta::apenasFinanceiro()
            ->where('tipo', 'pagar')
            ->where('status', 'pago')
            ->whereDate('data_vencimento', $hoje)
            ->sum('valor');

        // 3. Origem de Entradas Financeiras (Serviços e Combos)
        $faturamentoOrigem = [
            'corte_barba'    => Conta::apenasFinanceiro()->where('tipo', 'receber')->where('centro_custo', 'corte_barba')->where('status', 'pago')->sum('valor'),
            'combos_pacotes' => Conta::apenasFinanceiro()->where('tipo', 'receber')->where('centro_custo', 'combos_pacotes')->where('status', 'pago')->sum('valor'),
            'outros'         => Conta::apenasFinanceiro()->where('tipo', 'receber')->where('centro_custo', 'outros')->where('status', 'pago')->sum('valor'),
        ];

        // 4. Métricas Isoladas de Produtos e Estoque
        $produtosMetricas = [
            'faturamento_mensal' => Conta::apenasVendasProdutos()
                ->where('status', 'pago')
                ->whereMonth('data_vencimento', $mesAtual)
                ->whereYear('data_vencimento', $anoAtual)
                ->sum('valor'),

            'itens_vendidos_mes' => Conta::apenasVendasProdutos()
                ->where('status', 'pago')
                ->whereMonth('data_vencimento', $mesAtual)
                ->whereYear('data_vencimento', $anoAtual)
                ->sum('quantidade'),

            'faturamento_hoje' => Conta::apenasVendasProdutos()
                ->where('status', 'pago')
                ->whereDate('data_vencimento', $hoje)
                ->sum('valor'),

            'itens_vendidos_hoje' => Conta::apenasVendasProdutos()
                ->where('status', 'pago')
                ->whereDate('data_vencimento', $hoje)
                ->sum('quantidade'),
        ];

        // 5. Lista dos Últimos Lançamentos (Inclui todos os registros para histórico visual)
        $ultimosLancamentos = Conta::orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'saldoAtual',
            'totalRecebidoMensal',
            'totalPagoMensal',
            'totalPendenteMensal',
            'entradasHoje',
            'saidasHoje',
            'faturamentoOrigem',
            'produtosMetricas',
            'ultimosLancamentos'
        ));
    }
}