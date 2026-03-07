<template>
    <div class="stripe-element-wrapper">
        <div class="card-wrapper">
            <div ref="card" id="card-element" class="element">
                <!-- a Stripe Element will be inserted here. -->
            </div>
        </div>

        <div v-if="stripe_error_message" class="alert alert-danger" role="alert">{{stripe_error_message}}</div>
        <div id="card-errors" role="alert" class="error"></div>
    </div>
</template>

<script>
    export default {
        props: {
            stripe_key: { 
                type: String,
                required: true
            },
        },
        data: function() {
            return {
                stripe: null,
                elements: null, 
                card: null,
                stripe_error_message: '',
                stripe_token: null,
            }
        },
        created() {
            this.$emit('loading', true);
            console.log('stripe-element created');
            this.stripe = Stripe(this.stripe_key);
            this.elements = this.stripe.elements();
        },
        mounted() {
            console.log('stripe-element mounted');

            // Create the card
            let style = {
                base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            this.card = this.elements.create('card', {style: style});
            this.card.mount(this.$refs.card);
            this.card.addEventListener('change', this.stripe_card_change);
            
            this.$emit('loading', false);
        },
        methods: {
            stripe_card_change(event) {
                if (event.error) {
                    this.stripe_error_message = event.error.message;
                } else {
                    this.stripe_error_message = '';
                }
            },
            get_token() {
                // clear old errors
                this.stripe_error_message = "";

                this.$emit('loading', true);

                let self = this;

                this.stripe.createToken(this.card).then(function(result) {
                    self.$emit('loading', false);
                    if(result.error) {
                        self.stripe_error_message = result.error.message;
                        self.$forceUpdate(); // force dom update;
                    } else {
                        self.stripe_token = result.token.id;
                        self.$emit('token', result.token.id);
                        console.log('Got Token: ' + self.stripe_token);
                    }
                });
            },
        },
    }
</script>

<style scoped>
    .card-wrapper + .alert {
        margin-top: 1rem;
    }
</style>