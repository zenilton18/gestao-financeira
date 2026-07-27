

<?php $__env->startSection('content'); ?>

<div class="container">

    <h2 class="mb-4">
        Editar Produto
    </h2>


    <?php if(session('success')): ?>

        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>

    <?php endif; ?>



    
    <div class="card mb-4">

        <div class="card-header">
            Informações Shopee
        </div>


        <div class="card-body">

            <div class="row">


                <div class="col-md-3">

                    <?php if($produto->imagem): ?>

                        <img 
                            src="<?php echo e($produto->imagem); ?>"
                            class="img-fluid rounded"
                        >

                    <?php endif; ?>

                </div>



                <div class="col-md-9">


                    <div class="mb-3">

                        <label>
                            Nome do produto
                        </label>

                        <input 
                            class="form-control"
                            value="<?php echo e($produto->nome); ?>"
                            disabled
                        >

                    </div>



                    <div class="row">


                        <div class="col-md-6">

                            <label>
                                ID Shopee
                            </label>

                            <input 
                                class="form-control"
                                value="<?php echo e($produto->shopee_item_id); ?>"
                                disabled
                            >

                        </div>



                        <div class="col-md-6">

                            <label>
                                SKU Shopee
                            </label>

                            <input 
                                class="form-control"
                                value="<?php echo e($produto->sku); ?>"
                                disabled
                            >

                        </div>


                    </div>



                    <div class="mt-3">

                        <label>
                            Categoria Shopee
                        </label>

                        <input 
                            class="form-control"
                            value="<?php echo e($produto->categoria_id); ?>"
                            disabled
                        >

                    </div>


                </div>


            </div>


        </div>

    </div>





<form method="POST"
      action="<?php echo e(route('shopee.produtos.atualizar',$produto->id)); ?>">


<?php echo csrf_field(); ?>

<?php echo method_field('PUT'); ?>





<div class="card mb-4">

    <div class="card-header">
        Dados Internos ERP
    </div>


    <div class="card-body">


        <div class="row">


            <div class="col-md-4">

                <label>
                    Preço de custo
                </label>

                <input 
                    type="number"
                    step="0.01"
                    name="preco_custo"
                    class="form-control"
                    value="<?php echo e($produto->preco_custo); ?>"
                >

            </div>




            <div class="col-md-4">

                <label>
                    Preço venda
                </label>

                <input 
                    type="number"
                    step="0.01"
                    name="preco_venda"
                    class="form-control"
                    value="<?php echo e($produto->preco_venda); ?>"
                >

            </div>




            <div class="col-md-4">

                <label>
                    Estoque mínimo
                </label>

                <input 
                    type="number"
                    name="estoque_minimo"
                    class="form-control"
                    value="<?php echo e($produto->estoque_minimo); ?>"
                >

            </div>


        </div>





        <div class="row mt-3">


            <div class="col-md-6">

                <label>
                    Código interno
                </label>


                <input 
                    type="text"
                    name="codigo_interno"
                    class="form-control"
                    value="<?php echo e($produto->codigo_interno); ?>"
                >

            </div>



            <div class="col-md-6">

                <label>
                    Código barras
                </label>


                <input 
                    type="text"
                    name="codigo_barras"
                    class="form-control"
                    value="<?php echo e($produto->codigo_barras); ?>"
                >

            </div>


        </div>





        <div class="mt-3">


            <label>
                Localização estoque
            </label>


            <input 
                type="text"
                name="localizacao"
                class="form-control"
                value="<?php echo e($produto->localizacao); ?>"
            >


        </div>




        <div class="mt-3">


            <label>
                Observações
            </label>


            <textarea
                name="observacoes"
                class="form-control"
                rows="4"><?php echo e($produto->observacoes); ?></textarea>


        </div>


    </div>

</div>







<?php if($produto->variacoes->count()): ?>


<div class="card mb-4">


<div class="card-header">
    Variações
</div>


<div class="card-body">


<table class="table table-bordered">


<thead>

<tr>

<th>
Nome
</th>

<th>
SKU
</th>

<th>
Preço
</th>

<th>
Estoque
</th>

</tr>

</thead>


<tbody>


<?php $__currentLoopData = $produto->variacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>


<tr>


<td>
<?php echo e($variacao->nome); ?>

</td>


<td>
<?php echo e($variacao->sku); ?>

</td>


<td>
R$ <?php echo e(number_format($variacao->preco,2,',','.')); ?>

</td>


<td>
<?php echo e($variacao->estoque); ?>

</td>


</tr>


<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


</tbody>


</table>


</div>


</div>


<?php endif; ?>





<button type="submit"
        class="btn btn-primary">

Salvar alterações

</button>



<a href="<?php echo e(route('shopee.produtos.lista')); ?>"
   class="btn btn-secondary">

Voltar

</a>



</form>


</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\projetos\gestao-financeira\resources\views\shopee\produtos\editar.blade.php ENDPATH**/ ?>