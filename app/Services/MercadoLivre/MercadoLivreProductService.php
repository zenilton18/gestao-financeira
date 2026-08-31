<?php

namespace App\Services\MercadoLivre;

use App\Models\MercadoLivreConnection;
use RuntimeException;

class MercadoLivreProductService
{
    public function __construct(
        protected MercadoLivreApiService $api
    ) {
    }

    /**
     * Consulta um produto/anúncio no Mercado Livre.
     */
    public function getProduto(
        MercadoLivreConnection $connection,
        string $itemId
    ): array {
        return $this->api->get(
            $connection,
            "/items"
        );
    }

    public function getProdutos(
    MercadoLivreConnection $connection
): array {
    $userId = $connection->mercadolivre_user_id;

    $endpoint = "/users/{$userId}/items/search?search_type=scan";

    return $this->api->get(
        $connection,
        $endpoint
    );
}

    /**
     * Localiza a conexão ativa do Mercado Livre.
     */
    public function getConnection(
        int $userId
    ): MercadoLivreConnection {
        $connection = MercadoLivreConnection::where(
            'user_id',
            $userId
        )
            ->where('active', true)
            ->latest()
            ->first();

        if (!$connection) {
            throw new RuntimeException(
                'Nenhuma conta do Mercado Livre conectada.'
            );
        }

        return $connection;
    }
   
public function getUsuario(
    MercadoLivreConnection $connection
): array {
    return $this->api->get(
        $connection,
        '/users/me'
    );
}
}