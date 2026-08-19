<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $mesAtual = Carbon::now()->month;
        $anoAtual = Carbon::now()->year;
        $hoje = Carbon::today()->toDateString();

        // Filtro selecionado pelo usuário
        $filtro = $request->get('filtro');

        // =========================================================
        // 1. MÉTRICAS DO MÊS
        // =========================================================

        // Total recebido no mês
        $totalRecebidoMensal = Conta::apenasFinanceiro()
            ->where('tipo', 'receber')
            ->where('status', 'pago')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');

        // Total pago no mês
        $totalPagoMensal = Conta::apenasFinanceiro()
            ->where('tipo', 'pagar')
            ->where('status', 'pago')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');

        // Saldo atual
        $saldoAtual = $totalRecebidoMensal - $totalPagoMensal;

        // Total pendente no mês
        $totalPendenteMensal = Conta::apenasFinanceiro()
            ->where('status', 'pendente')
            ->whereMonth('data_vencimento', $mesAtual)
            ->whereYear('data_vencimento', $anoAtual)
            ->sum('valor');



        // =========================================================
        // 2. MOVIMENTAÇÕES DO DIA
        // =========================================================

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

        // =========================================================
        // 3. ORIGEM DE ENTRADAS FINANCEIRAS
        // =========================================================

        $faturamentoOrigem = [
            'corte_barba' => Conta::apenasFinanceiro()
                ->where('tipo', 'receber')
                ->where('centro_custo', 'corte_barba')
                ->where('status', 'pago')
                ->sum('valor'),

            'combos_pacotes' => Conta::apenasFinanceiro()
                ->where('tipo', 'receber')
                ->where('centro_custo', 'combos_pacotes')
                ->where('status', 'pago')
                ->sum('valor'),

            'outros' => Conta::apenasFinanceiro()
                ->where('tipo', 'receber')
                ->where('centro_custo', 'outros')
                ->where('status', 'pago')
                ->sum('valor'),
        ];

        // =========================================================
        // 4. MÉTRICAS DE PRODUTOS
        // =========================================================

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

        // =========================================================
        // 5. LISTAGEM DINÂMICA DOS LANÇAMENTOS
        // =========================================================

        $queryLancamentos = Conta::query();
      

        switch ($filtro) {

            // -----------------------------------------------------
            // RECEBIDO NO MÊS
            // -----------------------------------------------------
            case 'recebido':
                $queryLancamentos
                    ->apenasFinanceiro()
                    ->where('tipo', 'receber')
                    ->where('status', 'pago')
                    ->whereMonth('data_vencimento', $mesAtual)
                    ->whereYear('data_vencimento', $anoAtual);

                break;

            // -----------------------------------------------------
            // PAGO NO MÊS
            // -----------------------------------------------------
            case 'pago':
                $queryLancamentos
                    ->apenasFinanceiro()
                    ->where('tipo', 'pagar')
                    ->where('status', 'pago')
                    ->whereMonth('data_vencimento', $mesAtual)
                    ->whereYear('data_vencimento', $anoAtual);

                break;

            // -----------------------------------------------------
            // PENDENTE NO MÊS
            // -----------------------------------------------------
            case 'pendente':
                $queryLancamentos
                    ->apenasFinanceiro()
                    ->where('status', 'pendente')
                    ->whereMonth('data_vencimento', $mesAtual)
                    ->whereYear('data_vencimento', $anoAtual);

                break;

            // -----------------------------------------------------
            // FATURAMENTO DE PRODUTOS DO MÊS
            // -----------------------------------------------------
            case 'produtos_mes':
                $queryLancamentos
                    ->apenasVendasProdutos()
                    ->where('status', 'pago')
                    ->whereMonth('data_vencimento', $mesAtual)
                    ->whereYear('data_vencimento', $anoAtual);

                break;

            // -----------------------------------------------------
            // VENDAS DE PRODUTOS DE HOJE
            // -----------------------------------------------------
            case 'produtos_hoje':
                $queryLancamentos
                    ->apenasVendasProdutos()
                    ->where('status', 'pago')
                    ->whereDate('data_vencimento', $hoje);

                break;

            // -----------------------------------------------------
            // SEM FILTRO
            // -----------------------------------------------------
            default:
                $queryLancamentos = Conta::query();
                break;
        }

        // Se houver filtro, mostra até 50 registros.
        // Sem filtro, mantém o comportamento anterior de mostrar 5.
        if ($filtro) {
            $ultimosLancamentos = $queryLancamentos
                ->orderBy('data_vencimento', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();
        } else {
            $ultimosLancamentos = Conta::orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }



        return view('dashboard', compact(
            'saldoAtual',
            'totalRecebidoMensal',
            'totalPagoMensal',
            'totalPendenteMensal',
            'entradasHoje',
            'saidasHoje',
            'faturamentoOrigem',
            'produtosMetricas',
            'ultimosLancamentos',
            'filtro'
        ));
    }
}