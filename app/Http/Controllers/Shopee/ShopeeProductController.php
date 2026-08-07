<?php

namespace App\Http\Controllers\Shopee;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ShopeeConnection;
use App\Services\Shopee\ShopeeApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopeeProductController extends Controller
{
    public function __construct(
        protected ShopeeApiService $api
    ) {
    }

    /**
     * Tela de listagem (consulta o banco)
     */
    public function listarProdutosComDetalhes()
    {
        $produtos = Product::orderBy('nome')
            ->paginate(20);

        return view(
            'shopee.produtos.listaProdutos',
            compact('produtos')
        );
    }

    /**
     * Lista produtos da Shopee
     */
    private function listarProdutos()
    {
       
       
        return $this->api->get(
            '/api/v2/product/get_item_list',
            [
                'offset' => 0,
                'page_size' => 100,
                'item_status' => 'NORMAL'
            ]
        );
    }

    /**
     * Busca um produto
     */
    private function buscarProduto($itemId)
    {
        $resposta = $this->api->get(
            '/api/v2/product/get_item_base_info',
            [
                'item_id_list' => $itemId
            ]
        );

        return $resposta['response']['item_list'][0] ?? [];
    }

    /**
     * Busca variações
     */
    private function buscarVariacoes($itemId)
    {
        return $this->api->get(
            '/api/v2/product/get_model_list',
            [
                'item_id' => $itemId
            ]
        );
    }

    /**
     * Normaliza o produto
     */
    private function normalizarProduto(array $produto)
    {
        $possuiVariacao = $produto['has_model'];

        $variacoes = [];
        $estoque = 0;
        $preco = null;

        if ($possuiVariacao) {

            $models = $this->buscarVariacoes($produto['item_id']);

            foreach ($models['response']['model'] ?? [] as $model) {

                $variacoes[] = [

                    'model_id' => $model['model_id'],

                    'nome' => $model['model_name'],

                    'sku' => $model['model_sku'],

                    'preco' => $model['price_info'][0]['current_price'] ?? 0,

                    'estoque' => $model['stock_info_v2']['summary_info']['total_available_stock'] ?? 0,

                ];

                $estoque += $model['stock_info_v2']['summary_info']['total_available_stock'] ?? 0;
            }

            $preco = $variacoes[0]['preco'] ?? 0;

        } else {

            $estoque = $produto['stock_info_v2']['summary_info']['total_available_stock'] ?? 0;

            $preco = $produto['price_info'][0]['current_price'] ?? 0;
        }

        return [

            'item_id' => $produto['item_id'],

            'nome' => $produto['item_name'],

            'sku' => $produto['item_sku'],

            'marca' => $produto['brand']['original_brand_name'] ?? null,

            'categoria' => $produto['category_id'],

            'imagem' => $produto['image']['image_url_list'][0] ?? null,

            'status' => $produto['item_status'],

            'possui_variacao' => $possuiVariacao,

            'estoque_total' => $estoque,

            'preco_venda' => $preco,

            'peso' => $produto['weight'],

            'comprimento' => $produto['dimension']['package_length'],

            'largura' => $produto['dimension']['package_width'],

            'altura' => $produto['dimension']['package_height'],

            'variacoes' => $variacoes,
        ];
    }

    /**
     * Salva no banco
     */
    private function salvarProduto(array $produto)
    {
        echo('<pre>');
        print_r($produto);
        echo('</pre>'); die();
        $connection = ShopeeConnection::first();

        $product = Product::updateOrCreate(

            [
                'shopee_item_id' => $produto['item_id']
            ],

            [
                'shop_id' => $connection->shop_id,

                'nome' => $produto['nome'],

                'sku' => $produto['sku'],

                'marca' => $produto['marca'],

                'categoria_id' => $produto['categoria'],

                'imagem' => $produto['imagem'],

                'status' => $produto['status'],

                'possui_variacao' => $produto['possui_variacao'],

                'estoque_total' => $produto['estoque_total'],

                'peso' => $produto['peso'],

                'comprimento' => $produto['comprimento'],

                'largura' => $produto['largura'],

                'altura' => $produto['altura'],

                'preco_venda' => $produto['preco_venda'],
            ]
        );

        $product->variacoes()->delete();
        echo('<pre>');
        print_r($produto);
        echo('</pre>'); die();

        foreach ($produto['variacoes'] as $variacao) {

            ProductVariation::create([

                'product_id' => $product->id,

                'shopee_model_id' => $variacao['model_id'],

                'nome' => $variacao['nome'],

                'sku' => $variacao['sku'],

                'preco' => $variacao['preco'],
                'custo' => $variacao['custo'],

                'estoque' => $variacao['estoque'],
            ]);
        }
    }

    /**
     * Sincroniza com a Shopee
     */
    public function sincronizarProdutos()
    {
        $lista = $this->listarProdutos();

        foreach ($lista['response']['item'] as $item) {

            $produto = $this->buscarProduto($item['item_id']);

            if (empty($produto)) {
                continue;
            }

            $produto = $this->normalizarProduto($produto);

            $this->salvarProduto($produto);
        }

        return redirect('/shopee/produtos/lista')
            ->with('success', 'Produtos sincronizados com sucesso.');
    }
    public function editar($id)
    {
     
        $produto = Product::with('variacoes')
            ->findOrFail($id);

        return view(
            'shopee.produtos.editar',
            compact('produto')
        );
    }
    public function atualizar(Request $request, $id)
    {
        $produto = Product::findOrFail($id);

        // 1. Atualiza os dados principais do produto
        $produto->update([
            'preco_custo' => $request->preco_custo,
            'preco_venda' => $request->preco_venda,
            'codigo_interno' => $request->codigo_interno,
            'codigo_barras' => $request->codigo_barras,
            'estoque_minimo' => $request->estoque_minimo,
            'localizacao' => $request->localizacao,
            'observacoes' => $request->observacoes,
        ]);

        // 2. Atualiza o custo de cada variação enviada no formulário
        if ($request->has('variacoes')) {
            foreach ($request->input('variacoes') as $dadosVariacao) {
                $variacao = \App\Models\ProductVariation::where('id', $dadosVariacao['id'])
                    ->where('product_id', $produto->id)
                    ->first();

                if ($variacao) {
                    $variacao->update([
                        'custo' => $dadosVariacao['custo'] ?? 0
                    ]);
                }
            }
        }

        return redirect()
            ->route('shopee.produtos.editar', $produto->id)
            ->with('success', 'Produto atualizado com sucesso');
    }
}