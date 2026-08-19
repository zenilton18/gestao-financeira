<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopeeService
{
    private int $partnerId;
    private string $partnerKey;

    public function __construct()
    {
        $this->partnerId = (int) trim(env('SHOPEE_PARTNER_ID', 0));
        $this->partnerKey = trim(env('SHOPEE_PARTNER_KEY', ''));
    }

    public function getAuthorizationUrl(): string
    {
        $url = 'https://open.shopee.com.br/auth';
        $redirectUrl = 'https://meulucropro.com.br/shopee/callback';

        return $url
            . '?partner_id=' . $this->partnerId
            . '&redirect=' . urlencode($redirectUrl);
    }

    /**
     * Busca e mescla o PDF das etiquetas de uma lista de pedidos.
     *
     * @param array $ordersExternalIds Ex: ['240814XXXX1', '240814XXXX2']
     * @return string Conteúdo binário do PDF
     */
    public function getShippingDocuments(array $ordersExternalIds): string
    {
        // 1. Prepara a lista de pedidos no formato exigido pela API da Shopee
        $orderList = array_map(function ($orderId) {
            return [
                'order_sn' => $orderId,
                'document_type' => 'THERMAL_AIR_WAYBILL', // Ou 'NORMAL_AIR_WAYBILL' se for A4
            ];
        }, $ordersExternalIds);

        // 2. Solicita a criação dos documentos de envio
        $responseCreate = $this->makeApiRequest('POST', '/api/v2/logistics/create_shipping_document', [
            'order_list' => $orderList,
        ]);

        if (isset($responseCreate['error']) && !empty($responseCreate['error'])) {
            throw new \Exception("Erro ao solicitar etiquetas: " . ($responseCreate['message'] ?? $responseCreate['error']));
        }

        // Aguarda 1 segundo para a Shopee consolidar o arquivo
        sleep(1);

        // 3. Baixa o arquivo PDF compilado
        $pdfResponse = $this->makeRawApiRequest('POST', '/api/v2/logistics/download_shipping_document', [
            'order_list' => $orderList,
        ]);

        return $pdfResponse;
    }

    /**
     * Requisição com retorno em JSON
     */
    protected function makeApiRequest(string $method, string $path, array $data = []): array
    {
        $response = $this->executeHttpRequest($method, $path, $data);
        return $response->json() ?? [];
    }

    /**
     * Requisição com retorno bruto (PDF/Binary)
     */
    protected function makeRawApiRequest(string $method, string $path, array $data = []): string
    {
        $response = $this->executeHttpRequest($method, $path, $data);
        return $response->body();
    }

    /**
     * Executa a requisição HTTP com os cabeçalhos e assinatura da Shopee
     */
    private function executeHttpRequest(string $method, string $path, array $data = [])
    {
        // Ajuste a URL base conforme o seu ambiente (Sandbox ou Produção)
        $baseUrl = env('SHOPEE_API_URL', 'https://partner.shopeemobile.com');
        $url = $baseUrl . $path;

        // Adicione aqui a assinatura HMAC e Tokens conforme a sua implementação existente do ShopeeService
        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->$method($url, $data);
    }
}