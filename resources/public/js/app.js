function financeiro() {
    return {
        data_emissao: new Date().toISOString().split('T')[0],
        observacao: '',
        valor_total: '',
        quantidadeParcelas: 1,
        parcelas: [],

        gerarParcelas() {
            this.parcelas = [];
            let valorSugerido = this.valor_total ? (parseFloat(this.valor_total) / this.quantidadeParcelas).toFixed(2) : '';

            for (let i = 0; i < this.quantidadeParcelas; i++) {
                this.parcelas.push({
                    vencimento: '',
                    valor: valorSugerido,
                    situacao: 1,
                    mostrarPagamento: false,
                    data_pagamento: '',
                    valor_pago: '',
                    forma_pagamento: ''
                });
            }
        },

        alternarBotaoPagamento(index) {
            this.parcelas[index].mostrarPagamento = !this.parcelas[index].mostrarPagamento;
        },

       salvar() {
    // 1. Pega o token da tela
    let tokenElement = document.querySelector('meta[name="csrf-token"]');
    let token = tokenElement ? tokenElement.getAttribute('content') : null;

    // 2. Mostra no console se o token realmente existe ou se está vindo null
    console.log("TESTE DE TOKEN CSRF:", token);

    let payload = {
        data_emissao: this.data_emissao,
        observacao: this.observacao,
        valor_total: this.valor_total,
        quantidade_parcelas: this.quantidadeParcelas,
        parcelas: this.parcelas
    };
            fetch('http://gestao-financeira.test/financeiro/salvar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                if (response.status === 422) {
                    const erroValidacao = await response.json();
                    console.error('Erros de Validação do Laravel:', erroValidacao.errors);
                    
                    let mensagens = Object.values(erroValidacao.errors).flat().join('\n');
                    alert("Falha na validação dos dados:\n\n" + mensagens);
                    throw new Error("Validação falhou.");
                }
                
                if (!response.ok) {
                    throw new Error("Erro no servidor: " + response.status);
                }
                
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // window.location.href = '/financeiro';
                }
            })
            .catch(error => {
                console.error('Erro completo:', error);
            });
        },

        init() {
            this.gerarParcelas();
        }
    }
}