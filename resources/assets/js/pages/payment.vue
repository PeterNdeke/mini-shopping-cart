<template>
    <div class="column is-half is-offset-one-quarter">
        <h2 class="title is-3 mb-4">Checkout Summary</h2>
        <div class="card card-content">
            <p class="title is-4 mb-4" v-if="cart.length == 0">Your cart is empty</p>
            <div v-for="item in cart" :key="item.id" v-else>
                <article class="media mb-4">
                    <figure class="media-left">
                        <p class="image is-128x128">
                            <img :src="item.product.image">
                        </p>
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <div>
                                <strong class="is-size-4">{{ item.product.parent.title }}</strong>
                                <p class="mb-4">{{ item.product.parent.description }}</p>
                                <p>${{ item.product.price }} X {{ item.quantity }}
                                  
                                </p>
                                <p>Total for this item: ${{ item.product.price * item.quantity }}</p>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <div class="mb-2" v-if="cart.length > 0">
                <p class="title is-4 has-text-right">Total: ${{ total }}</p>
            </div>
            <div class="flex">
               
                  <paystack
        :amount="total * 100"
        :email="user.email"
        :paystackkey="paystackkey"
        :reference="reference"
        :callback="callback"
        :close="close"
        :embed="false"
         class="button is-info is-block is-large w-half"
    >
       <i class="fas fa-money-bill-alt"></i>
      Pay with Paystack
    </paystack>
               
              
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { EventBus } from "~/bus.js";
import paystack from 'vue-paystack';

export default {
  name: "Cart",
  data() {
    return {
      loading: false,
       paystackkey: "pk_test_2e2e6231059150310cb92e9becbd64ba8ff8c2c8", //paystack public key
       payment_response: ''
    };
  },
  components: {
        paystack
    },
  computed: {
    ...mapGetters({
      cart: "cart/cart",
      user: "auth/user",
      total: "cart/cartTotal"
    }),
     reference(){
        let text = "";
        let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( let i=0; i < 10; i++ )
          text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
      }
  },
  methods: {
    ...mapActions({
     // removeProductFromCart: "cart/removeProductFromCart",
      removeAllProductsFromCart: "cart/removeAllProductsFromCart"
    }),
    callback: function(response){
        console.log(response)
      this.payment_response = response;
      this.payViaCard();
      },
      close: function(){
          console.log("Payment closed")
      },

       payViaCard(){
      const app = this;
      const param = {
        order_id: app.$route.params.id,
        payment_refernce: app.payment_response.reference,
      };
       if (app.payment_response.message === 'Approved') {

        this.loading = true;
        const total = this.total;
        const count = this.count;
         axios.post("/api/v1/update-order", param).then((res)=>{
           console.log(res)
         });
       
        this.$router.push({ name: 'home' })
          this.$store.dispatch(
          "noti",
          {
             message:
            "Your purchase has been processed, please check your email!",
            type: "is-success"
           },
           { root: true }
         );
         this.removeAllProductsFromCart();
      
       }
      }
  }
};
</script>