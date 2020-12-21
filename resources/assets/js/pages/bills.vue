<template>
    <div class="column is-half is-offset-one-quarter">
        <h2 class="title is-3 mb-4">Bills Payment</h2>
        <div class="card card-content">
          
       
              
                        
                  
                         
                            <form @submit.prevent="makePayment">
                            <div class="field">
                              
                              <div class="control">
              <div class="select">
               <select class="is-focused is-large">
              <option>Select Network</option>
               <option>MTN</option>
                <option>Airtel</option>
                 <option>Glo</option>
             </select>
                  </div>
                  </div>
                              
                                    <!-- <p class="help is-danger" v-if="errors.email" v-text="errors.email[0]"></p> -->
                              
                            </div>
                            <div class="field">
                              
                                <input
                                    class="input is-large"
                                    type="text"
                                    placeholder="type phone number"
                                    v-model="phoneNumber">
                                    <!-- <p class="help is-danger" v-if="errors.password" v-text="errors.password[0]"></p> -->
                               
                            </div>

                            <div class="field">
                              
                                <input
                                    class="input is-large"
                                    type="number"
                                    placeholder="type ammount"
                                    v-model.number="amount">
                                    <!-- <p class="help is-danger" v-if="errors.password" v-text="errors.password[0]"></p> -->
                               
                            </div>
                          
                        
                            <button v-if="user" type="submit" class="button is-block is-info is-large is-fullwidth">Proceed</button>
                             <button v-else class="button is-block is-info is-large is-fullwidth"  @click.prevent="signIn">Login to continue</button>
                            </form>
                      
                        <!-- <p class="has-text-grey">
                            <a href="" @click.prevent="signUp" :class="{ 'is-loading': loading }">Sign Up</a>
                        </p> -->
                 
           
            
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { EventBus } from "~/bus.js";
export default {
  name: "Cart",
  data() {
    return {
      loading: false,
      amount: 200,
      billType: '',
      phoneNumber: '',
      payment_response: ''
    };
  },
  computed: {
    ...mapGetters({
      cart: "cart/cart",
      user: "auth/user",
      total: "cart/cartTotal",
      count: "cart/cartItemCount",
    })
  },
  methods: {
 
    signIn() {
      EventBus.$emit("sign-in", true);
    },
   
     message() {
          this.$store.dispatch(
          "noti",
          {
             message:
            "Your airtime  purchase has been processed",
            type: "is-success"
           },
           { root: true }
         );
     },
    makePayment() {
     const email =   this.user.email
     const name = this.user.name
     const phoneNumber = this.phoneNumber
     const redirect = this.$router.push({ name: 'home' })
     const message = this.message();
      this.$launchFlutterwave({
        tx_ref: Date.now(),
        amount: this.amount,
        currency: 'NGN',
        payment_options: 'card,ussd',
        customer: {
          email: email ,
          phone_number: phoneNumber,
          name: name
        },
        callback: function(data) {
          // specified callback function
          console.log(data)
        const param = {
        
        transaction_id: data.transaction_id,
      };
        this.payment_response = data;
        //this.payBills();
        if (data.status === 'successful') {
         axios.post("/api/v1/payment-verify", param).then((res)=>{
           console.log(res)
         });
       
          redirect;
          message;
          window.location.href = '/';
         

      
       }
        },
        customizations: {
          title: 'My store',
          description: 'Payment for items in cart',
          logo: 'https://assets.piedpiper.com/logo.png'
        },

        
      })
    },
     
    payBills(){
      const app = this;
     
      const param = {
        
        transaction_id: app.payment_response.transaction_id,
      };
       if (app.payment_response.status === 'successful') {
         axios.post("/api/v1/payment-verify", param).then((res)=>{
           console.log(res)
         });
       
        this.$router.push({ name: 'home' })
          this.$store.dispatch(
          "noti",
          {
             message:
            "Your airtime  purchase has been processed",
            type: "is-success"
           },
           { root: true }
         );

      
       }
      }
  }
};
</script>