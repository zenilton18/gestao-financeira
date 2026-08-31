
<form id="form-etiquetas"
      action="{{ route('shopee.orders.etiquetas') }}"
      method="POST">
    @csrf

    <!-- Barra de Ações em Lote -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div class="form-check ms-2">
            <input
                class="form-check-input"
                type="checkbox"
                id="select-all"
            >

            <label
                class="form-check-label fw-semibold"
                for="select-all"
            >
                Selecionar Todos
            </label>
        </div>

        <button
            type="submit"
            id="btn-imprimir-etiquetas"
            class="btn btn-outline-primary btn-sm"
            disabled
        >
            <i class="bi bi-printer me-1"></i>

            <span id="btn-imprimir-texto">
                Imprimir Etiquetas Selecionadas
            </span>

            (<span id="selected-count">0</span>)
        </button>

    </div>


    <div class="card shadow-sm border-0">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th style="width: 40px;" class="text-center">#</th>
                            <th>Pedido</th>
                            <th>Marketplace</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th class="text-end">Venda</th>
                            <th class="text-end">Lucro</th>
                            <th class="text-center">Margem</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>

                    </thead>


                    <tbody>

                    @forelse($orders as $order)

                        <tr>

                            <td class="text-center">

                                <input
                                    class="form-check-input order-checkbox"
                                    type="checkbox"
                                    name="order_ids[]"
                                    value="{{ $order->id }}"
                                >

                            </td>


                            <td>
                                <strong>
                                    {{ $order->pedido_externo }}
                                </strong>
                            </td>


                            <td>
                                <span class="badge bg-success">
                                    Shopee
                                </span>
                            </td>


                            <td>
                                <div class="fw-semibold">
                                    {{ $order->usuario_cliente }}
                                </div>
                            </td>


                            <td>

                                {{ $order->data_pedido?->format('d/m/Y') }}

                                <br>

                                <small class="text-muted">
                                    {{ $order->data_pedido?->format('H:i') }}
                                </small>

                            </td>


                            <td class="text-end">

                                <strong>
                                    R$
                                    {{ number_format(
                                        $order->valor_produtos,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </strong>

                            </td>


                            <td class="text-end">

                                <span class="{{ $order->lucro_bruto >= 0
                                    ? 'text-success'
                                    : 'text-danger' }} fw-bold">

                                    R$
                                    {{ number_format(
                                        $order->lucro_bruto,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                </span>

                            </td>


                            <td class="text-center">

                                @php

                                    $calculoMargem =
                                        ($order->valor_total ?? 0) > 0
                                            ? (
                                                ($order->lucro_bruto ?? 0)
                                                /
                                                $order->valor_total
                                            ) * 100
                                            : 0;

                                @endphp


                                @include(
                                    'shopee.orders.components.margin-badge',
                                    [
                                        'margin' => $calculoMargem
                                    ]
                                )

                            </td>


                            <td class="text-center">

                                @include(
                                    'shopee.orders.components.status-badge',
                                    [
                                        'status' =>
                                            $order->status_marketplace
                                    ]
                                )

                            </td>


                            <td class="text-center">

                                @include(
                                    'shopee.orders.components.actions'
                                )

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="text-center py-5"
                            >

                                <i
                                    class="bi bi-inbox display-5 d-block mb-3 text-muted"
                                ></i>

                                Nenhum pedido encontrado.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</form>


<div class="mt-4">

    {{ $orders->links() }}

</div>


<!-- ============================================================
     QZ TRAY
     ============================================================ -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/qz-tray/2.2.5/qz-tray.js"></script>


<!-- ============================================================
     PDF.JS
     Usaremos para transformar as páginas do PDF em imagens.
     ============================================================ -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
     * =========================================================
     * ELEMENTOS
     * =========================================================
     */

    const form =
        document.getElementById('form-etiquetas');

    const selectAllCheckbox =
        document.getElementById('select-all');

    const orderCheckboxes =
        document.querySelectorAll('.order-checkbox');

    const printBtn =
        document.getElementById('btn-imprimir-etiquetas');

    const selectedCountSpan =
        document.getElementById('selected-count');

    const printBtnText =
        document.getElementById('btn-imprimir-texto');


    /*
     * =========================================================
     * CONFIGURAÇÃO PDF.JS
     * =========================================================
     */

    if (window.pdfjsLib) {

        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    }


    /*
     * =========================================================
     * CONTADOR
     * =========================================================
     */

    function updateCounter() {

        const checkedCount =
            document.querySelectorAll(
                '.order-checkbox:checked'
            ).length;

        selectedCountSpan.textContent =
            checkedCount;

        printBtn.disabled =
            checkedCount === 0;
    }


    /*
     * =========================================================
     * SELECIONAR TODOS
     * =========================================================
     */

    if (selectAllCheckbox) {

        selectAllCheckbox.addEventListener(
            'change',
            function () {

                orderCheckboxes.forEach(
                    cb => {

                        cb.checked =
                            selectAllCheckbox.checked;

                    }
                );

                updateCounter();

            }
        );

    }


    /*
     * =========================================================
     * CHECKBOX INDIVIDUAL
     * =========================================================
     */

    orderCheckboxes.forEach(
        cb => {

            cb.addEventListener(
                'change',
                function () {

                    if (!this.checked) {

                        selectAllCheckbox.checked =
                            false;

                    } else if (
                        document.querySelectorAll(
                            '.order-checkbox:checked'
                        ).length ===
                        orderCheckboxes.length
                    ) {

                        selectAllCheckbox.checked =
                            true;

                    }

                    updateCounter();

                }
            );

        }
    );


    /*
     * =========================================================
     * CONECTAR AO QZ TRAY
     * =========================================================
     */

    async function conectarQZ() {

        if (!window.qz) {

            throw new Error(
                'QZ Tray não foi carregado.'
            );

        }


        if (qz.websocket.isActive()) {

            return;

        }


        await qz.websocket.connect();

    }


    /*
     * =========================================================
     * LOCALIZAR IMPRESSORA
     * =========================================================
     */

    async function localizarImpressora() {

        const impressoras =
            await qz.printers.find();

        console.log(
            'Impressoras encontradas:',
            impressoras
        );


        /*
         * Primeiro tenta encontrar JK-402A.
         */

        const impressoraJK =
            impressoras.find(
                impressora =>
                    impressora
                        .toUpperCase()
                        .includes('JK-402A')
            );


        if (impressoraJK) {

            return impressoraJK;

        }


        /*
         * Caso não encontre automaticamente,
         * usamos a impressora padrão do Windows.
         */

        const impressoraPadrao =
            await qz.printers.getDefault();

        if (impressoraPadrao) {

            return impressoraPadrao;

        }


        throw new Error(
            'Nenhuma impressora encontrada.'
        );

    }


    /*
     * =========================================================
     * PDF → IMAGENS
     * =========================================================
     */

    async function pdfParaImagens(pdfBlob) {

        const arrayBuffer =
            await pdfBlob.arrayBuffer();


        const pdf =
            await pdfjsLib.getDocument({
                data: arrayBuffer
            }).promise;


        const imagens = [];


        for (
            let numeroPagina = 1;
            numeroPagina <= pdf.numPages;
            numeroPagina++
        ) {

            console.log(
                `Renderizando etiqueta ${numeroPagina}/${pdf.numPages}`
            );


            const pagina =
                await pdf.getPage(
                    numeroPagina
                );


            /*
             * Escala alta para manter
             * boa qualidade na térmica.
             */

            const viewport =
                pagina.getViewport({
                    scale: 2
                });


            const canvas =
                document.createElement(
                    'canvas'
                );


            const contexto =
                canvas.getContext(
                    '2d'
                );


            canvas.width =
                viewport.width;

            canvas.height =
                viewport.height;


            await pagina.render({
                canvasContext:
                    contexto,

                viewport:
                    viewport
            }).promise;


            /*
             * JPEG em qualidade alta.
             */

            const imagem =
                canvas.toDataURL(
                    'image/png'
                );


            imagens.push(
                imagem
            );

        }


        return imagens;

    }


    /*
     * =========================================================
     * IMPRIMIR IMAGENS NO QZ TRAY
     * =========================================================
     */

    async function imprimirImagens(
        imagens,
        nomeImpressora
    ) {

        console.log(
            'Impressora:',
            nomeImpressora
        );


        /*
         * Configuração da impressora.
         */

        const config =
            qz.configs.create(
                nomeImpressora,
                {
                    size: {
                        width: 100,
                        height: 150
                    },

                    units: 'mm',

                    density: 203,

                    margins: 0,

                    scaleContent: false,

                    rasterize: true
                }
            );


        /*
         * Cada imagem representa
         * uma etiqueta.
         */

        const dados =
            imagens.map(
                imagem => ({

                    type: 'pixel',

                    format: 'image',

                    flavor: 'base64',

                    data:
                        imagem.split(',')[1]

                })
            );


        await qz.print(
            config,
            dados
        );

    }


    /*
     * =========================================================
     * IMPRESSÃO COMPLETA
     * =========================================================
     */

    async function imprimirEtiquetasAutomaticamente() {

        /*
         * Pedidos selecionados
         */

        const selecionados =
            Array.from(
                document.querySelectorAll(
                    '.order-checkbox:checked'
                )
            );


        if (
            selecionados.length === 0
        ) {

            alert(
                'Selecione pelo menos um pedido.'
            );

            return;

        }


        /*
         * Interface
         */

        printBtn.disabled =
            true;

        printBtnText.textContent =
            'Gerando etiquetas...';


        try {

            /*
             * =================================================
             * 1. CONECTA QZ
             * =================================================
             */

            printBtnText.textContent =
                'Conectando à impressora...';


            await conectarQZ();


            /*
             * =================================================
             * 2. ENCONTRA IMPRESSORA
             * =================================================
             */

            const impressora =
                await localizarImpressora();


            console.log(
                'Impressora selecionada:',
                impressora
            );


            /*
             * =================================================
             * 3. MONTA FORM DATA
             * =================================================
             */

            const formData =
                new FormData();


            /*
             * CSRF
             */

            const csrf =
                form.querySelector(
                    'input[name="_token"]'
                );


            if (csrf) {

                formData.append(
                    '_token',
                    csrf.value
                );

            }


            /*
             * Pedidos
             */

            selecionados.forEach(
                checkbox => {

                    formData.append(
                        'order_ids[]',
                        checkbox.value
                    );

                }
            );


            /*
             * =================================================
             * 4. ENVIA AO LARAVEL
             * =================================================
             */

            printBtnText.textContent =
                'Baixando etiquetas...';


            const response =
                await fetch(
                    form.action,
                    {
                        method: 'POST',

                        body: formData,

                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest',

                            'Accept':
                                'application/pdf'
                        }
                    }
                );


            /*
             * Verifica resposta.
             */

            if (!response.ok) {

                const texto =
                    await response.text();

                console.error(
                    texto
                );

                throw new Error(
                    'O servidor não conseguiu gerar as etiquetas.'
                );

            }


            /*
             * =================================================
             * 5. PEGA PDF
             * =================================================
             */

            const pdfBlob =
                await response.blob();


            if (
                pdfBlob.type &&
                !pdfBlob.type.includes(
                    'pdf'
                )
            ) {

                throw new Error(
                    'A resposta recebida não é um PDF.'
                );

            }


            /*
             * =================================================
             * 6. CONVERTE PDF EM IMAGENS
             * =================================================
             */

            printBtnText.textContent =
                'Preparando etiquetas...';


            const imagens =
                await pdfParaImagens(
                    pdfBlob
                );


            if (
                !imagens.length
            ) {

                throw new Error(
                    'Nenhuma etiqueta foi encontrada no PDF.'
                );

            }


            /*
             * =================================================
             * 7. ENVIA PARA IMPRESSORA
             * =================================================
             */

            printBtnText.textContent =
                `Imprimindo ${imagens.length} etiqueta(s)...`;


            await imprimirImagens(
                imagens,
                impressora
            );


            /*
             * =================================================
             * 8. SUCESSO
             * =================================================
             */

            printBtnText.textContent =
                'Impressão enviada!';


            /*
             * Pequeno atraso apenas
             * para o usuário visualizar.
             */

            setTimeout(
                () => {

                    printBtnText.textContent =
                        'Imprimir Etiquetas Selecionadas';

                    printBtn.disabled =
                        false;

                },
                2000
            );


        } catch (error) {

            console.error(
                'Erro na impressão:',
                error
            );


            alert(
                'Não foi possível imprimir as etiquetas.\n\n'
                + error.message
            );


            printBtnText.textContent =
                'Imprimir Etiquetas Selecionadas';

            printBtn.disabled =
                false;

        }

    }


    /*
     * =========================================================
     * SUBMIT
     * =========================================================
     */

    form.addEventListener(
        'submit',
        function (event) {

            /*
             * IMPORTANTE:
             *
             * Impede o navegador de abrir
             * o PDF em outra aba.
             */

            event.preventDefault();


            imprimirEtiquetasAutomaticamente();

        }
    );

});

</script>
