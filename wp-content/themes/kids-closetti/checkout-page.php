<?php /* Template Name: checkout-template */ ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title><?php the_field('title'); ?></title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php themePath('/assets/styles/checkout.min.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="icon" href="<?php themePath('/favicon.ico'); ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php themePath('/favicon.ico'); ?>" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

</html>

<script>
    window.checkoutFields = <?php echo json_encode(get_fields()); ?>;
</script>

<body>
    <section id="checkout">
        <div class="top-content ">
            <div class="container">
                <img class="logo" src="<?php themePath('/assets/images/logo.png'); ?>" />
            </div>
        </div>
        <section class="form-content">
            <div class="container flex-row-sm-col">
                <div class="card-info">
                    <div class="container-fluid">
                        <h3 class="montserrat">Dados pessoais</h3>
                        <div class="input-group">
                            <label>Razão social</label>
                            <input type="text" v-model="personalInfo.name" placeholder="Digite sua razão social..." :disabled="isSubmiting" />
                        </div>
                        <div class="input-group">
                            <label>Email</label>
                            <input type="email" v-model="personalInfo.email" placeholder="Digite seu email..." :disabled="isSubmiting" />
                        </div>
                        <div class="input-group">
                            <label>CNPJ</label>
                            <input type="text" v-model="personalInfo.docNumber" placeholder="Digite seu CNPJ..." @keyup="makeMask('personalInfo.docNumber','##.###.###/####-##')" :disabled="isSubmiting" />
                        </div>
                    </div>
                </div>
                <div class="card-info">
                    <div class="container-fluid">
                        <h3 class="montserrat">Pagamento</h3>
                        <div class="payment-methods-selection">
                            <label v-for="(method,i) in checkoutFields.payment_methods" :key='i' :class="`item ${paymentInfo.method == method ? 'active' : ''}`" @click="setPaymentMethod(method)">
                                {{method}}
                            </label>
                        </div>
                        <template v-if="paymentInfo.method === 'Cartão de crédito'">
                            <div class="input-group">
                                <label>Nome impresso</label>
                                <input type="text" class="uppercase" v-model="paymentInfo.creditcard.name" placeholder="Digite o nome..." :disabled="isSubmiting" />
                            </div>
                            <div class="input-group">
                                <label>Número do cartão</label>
                                <input type="text" v-model="paymentInfo.creditcard.number" placeholder="Digite o número..." @keyup="makeMask('paymentInfo.creditcard.number','#### #### #### ####')" :disabled="isSubmiting" />
                            </div>
                            <div class="input-group">
                                <label>Vencimento</label>
                                <input type="text" v-model="paymentInfo.creditcard.dueDate" placeholder="Digite o vencimento..." @keyup="makeMask('paymentInfo.creditcard.dueDate','##/##')" :disabled="isSubmiting" />
                            </div>
                            <div class="input-group">
                                <label>CVV</label>
                                <input type="text" v-model="paymentInfo.creditcard.cvv" placeholder="Digite o CVV..." @keyup="makeMask('paymentInfo.creditcard.cvv','####')" :disabled="isSubmiting" />
                            </div>
                        </template>
                    </div>
                </div>
                <div class="card-info">
                    <div class="container-fluid description-content">
                        <h3 class="montserrat"><?php the_field('product_name'); ?></h3>
                        <div class="flex-column mb-20">
                            <?php the_field("description"); ?>
                            <b class="text-content">Tipo de cobrança : <?php the_field('type'); ?></b>
                            <b class="text-content">{{formatedPrice}} - ({{paymentInfo.method}})</b>
                        </div>
                        <button class="btn-submit" @click="submit" :disabled="isSubmiting">
                            {{isSubmiting ? 'Aguarde...' : 'Finalizar compra'}}
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <script src='<?php themePath('/assets/js/vue3.min.js'); ?>'></script>
    <script src='<?php themePath('/assets/js/init-checkout.js'); ?>'></script>
    <script src='<?php themePath('/assets/js/checkout.js'); ?>'></script>
</body>
<?php get_footer(); ?>

</html>