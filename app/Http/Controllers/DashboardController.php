<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $agora = Carbon::now();

        $mesAtual = $agora->month;
        $anoAtual = $agora->year;
        $hoje = $agora->copy()->startOfDay();

        /*
         * =========================================================
         * FILTROS DO DASHBOARD
         * =========================================================
         */

        $filtro = $request->get('filtro');

        $periodo = $request->get('periodo', 'mes');

        if (!in_array($periodo, ['dia', 'semana', 'mes'])) {
            $periodo = 'mes';
        }

        /*
         * Data selecionada no gráfico.
         *
         * Se não vier nenhuma data, usamos hoje.
         */
        $dataSelecionada = $request->get('data');

        try {
            $dataSelecionada = $dataSelecionada
                ? Carbon::parse($dataSelecionada)->startOfDay()
                : $hoje->copy();
        } catch (\Exception $e) {
            $dataSelecionada = $hoje->copy();
        }


        // =========================================================
        // 1. MÉTRICAS DO MÊS
        // =========================================================

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


        // =========================================================
        // 2. MOVIMENTAÇÕES DE HOJE
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
        // 3. ORIGEM DAS ENTRADAS
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
        // 5. GRÁFICO FINANCEIRO
        //
        // DIA    = mostra os lançamentos do dia selecionado
        // SEMANA = mostra cada dia da semana selecionada
        // MÊS    = mostra cada dia do mês atual
        // =========================================================

        $labelsGrafico = [];
        $entradasGrafico = [];
        $saidasGrafico = [];

        $datasGrafico = [];


        // ---------------------------------------------------------
        // PERÍODO DIA
        // ---------------------------------------------------------

        if ($periodo === 'dia') {

            $inicio = $dataSelecionada->copy()->startOfDay();
            $fim = $dataSelecionada->copy()->endOfDay();

            $datasGrafico[] = $dataSelecionada->copy();

            $labelsGrafico[] = $dataSelecionada->format('d/m');

        }


        // ---------------------------------------------------------
        // PERÍODO SEMANA
        // ---------------------------------------------------------

        elseif ($periodo === 'semana') {

            $inicio = $dataSelecionada->copy()->startOfWeek();
            $fim = $dataSelecionada->copy()->endOfWeek();

            $data = $inicio->copy();

            while ($data->lte($fim)) {

                $datasGrafico[] = $data->copy();

                $labelsGrafico[] = $data->format('d/m');

                $data->addDay();
            }

        }


        // ---------------------------------------------------------
        // PERÍODO MÊS
        // ---------------------------------------------------------

        else {

            $inicio = $agora->copy()->startOfMonth();
            $fim = $agora->copy()->endOfMonth();

            $data = $inicio->copy();

            while ($data->lte($fim)) {

                $datasGrafico[] = $data->copy();

                $labelsGrafico[] = $data->format('d');

                $data->addDay();
            }
        }


        /*
         * Busca todas as movimentações necessárias
         * de uma única vez.
         */

        $movimentacoesGrafico = Conta::apenasFinanceiro()
            ->where('status', 'pago')
            ->whereBetween('data_vencimento', [
                $inicio->toDateString(),
                $fim->toDateString(),
            ])
            ->get();


        /*
         * Monta os valores de cada dia.
         */

        foreach ($datasGrafico as $data) {

            $dataString = $data->format('Y-m-d');

            $entrada = $movimentacoesGrafico
                ->where('tipo', 'receber')
                ->filter(function ($item) use ($dataString) {
                    return Carbon::parse($item->data_vencimento)
                        ->format('Y-m-d') === $dataString;
                })
                ->sum('valor');


            $saida = $movimentacoesGrafico
                ->where('tipo', 'pagar')
                ->filter(function ($item) use ($dataString) {
                    return Carbon::parse($item->data_vencimento)
                        ->format('Y-m-d') === $dataString;
                })
                ->sum('valor');


            $entradasGrafico[] = (float) $entrada;

            $saidasGrafico[] = (float) $saida;
        }


        // =========================================================
        // 6. DADOS DO DIA/SEMANA/MÊS SELECIONADO
        // =========================================================

        if ($periodo === 'dia') {

            $inicioSelecionado = $dataSelecionada->copy()->startOfDay();
            $fimSelecionado = $dataSelecionada->copy()->endOfDay();

        } elseif ($periodo === 'semana') {

            $inicioSelecionado = $dataSelecionada->copy()->startOfWeek();
            $fimSelecionado = $dataSelecionada->copy()->endOfWeek();

        } else {

            $inicioSelecionado = $dataSelecionada->copy()->startOfMonth();
            $fimSelecionado = $dataSelecionada->copy()->endOfMonth();
        }


        /*
         * Entradas do período selecionado.
         */

        $entradasDataSelecionada = Conta::apenasFinanceiro()
            ->where('tipo', 'receber')
            ->where('status', 'pago')
            ->whereBetween('data_vencimento', [
                $inicioSelecionado->toDateString(),
                $fimSelecionado->toDateString(),
            ])
            ->sum('valor');


        /*
         * Saídas do período selecionado.
         */

        $saidasDataSelecionada = Conta::apenasFinanceiro()
            ->where('tipo', 'pagar')
            ->where('status', 'pago')
            ->whereBetween('data_vencimento', [
                $inicioSelecionado->toDateString(),
                $fimSelecionado->toDateString(),
            ])
            ->sum('valor');


        /*
         * Lançamentos do período selecionado.
         */

        $lancamentosDataSelecionada = Conta::apenasFinanceiro()
            ->whereBetween('data_vencimento', [
                $inicioSelecionado->toDateString(),
                $fimSelecionado->toDateString(),
            ])
            ->orderBy('data_vencimento')
            ->orderBy('created_at')
            ->get();


        // =========================================================
        // 7. LISTAGEM PRINCIPAL
        // =========================================================

        $queryLancamentos = Conta::query();


        switch ($filtro) {

            case 'recebido':

                $queryLancamentos
                    ->apenasFinanceiro()
                    ->where('tipo', 'receber')
                    ->where('status', 'pago')
                    ->whereMonth('data_vencimento', $mesAtual)
                    ->whereYear('data_vencimento', $anoAtual);

                break;


            case 'pago':

                $queryLancamentos
                    ->apenasFinanceiro()
                    ->where('tipo', 'pagar')
                    ->where('status', 'pago')
                    ->whereMonth('data_vencimento', $mesAtual)
                    ->whereYear('data_vencimento', $anoAtual);

                break;


            case 'pendente':

                $queryLancamentos
                    ->apenasFinanceiro()
                    ->where('status', 'pendente')
                    ->whereMonth('data_vencimento', $mesAtual)
                    ->whereYear('data_vencimento', $anoAtual);

                break;


            case 'produtos_mes':

                $queryLancamentos
                    ->apenasVendasProdutos()
                    ->where('status', 'pago')
                    ->whereMonth('data_vencimento', $mesAtual)
                    ->whereYear('data_vencimento', $anoAtual);

                break;


            case 'produtos_hoje':

                $queryLancamentos
                    ->apenasVendasProdutos()
                    ->where('status', 'pago')
                    ->whereDate('data_vencimento', $hoje);

                break;


            default:

                $queryLancamentos = Conta::query();

                break;
        }


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


        // =========================================================
        // 8. RETORNO DA VIEW
        // =========================================================

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

            'filtro',

            'periodo',

            'dataSelecionada',

            'labelsGrafico',

            'entradasGrafico',

            'saidasGrafico',

            'entradasDataSelecionada',

            'saidasDataSelecionada',

            'lancamentosDataSelecionada',

            'inicioSelecionado',

            'fimSelecionado'
        ));
    }
}