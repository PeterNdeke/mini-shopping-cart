<template>
    <b-modal :active.sync="active" has-modal-card :width="500">
        <div class="hero-body">
            <div class="container has-text-centered">
                <div class="column is-full">
                    
                    <div class="box">
                      <h3 class="title has-text-grey">Register</h3>
                    <p class="subtitle has-text-grey">Please register to proceed.</p>
                        <form @submit.prevent="register">
                        <div class="field">
                            <div class="control">
                            <input
                                class="input is-large"
                                type="text"
                                placeholder="Your Name"
                                autofocus
                                v-model="name">
                                    <p class="help is-danger" v-if="errors.name" v-text="errors.name[0]"></p>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                            <input
                                class="input is-large"
                                type="email"
                                placeholder="Your Email"
                                v-model="email">
                                <p class="help is-danger" v-if="errors.email" v-text="errors.email[0]"></p>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                            <input
                                class="input is-large"
                                type="password"
                                placeholder="Your Password"
                                v-model="password">
                                <p class="help is-danger" v-if="errors.password" v-text="errors.password[0]"></p>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                            <input
                                class="input is-large"
                                type="password"
                                placeholder="Your Password Confirmation"
                                v-model="password_confirmation">
                                <p class="help is-danger" v-if="errors.password_confirmation" v-text="errors.password_confirmation[0]"></p>
                            </div>
                        </div>

                        <button type="submit" class="button is-block is-info is-large is-fullwidth">Register</button>
                        </form>
                    </div>
                    <p class="has-text-grey">
                        <a href="" @click.prevent="signIn">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
import { mapGetters } from "vuex";
import { EventBus } from "~/bus.js";
export default {
  name: "Register",
  data: () => ({
    active: false,
    name: "",
    email: "",
    password: "",
    password_confirmation: ""
  }),
  created() {
    EventBus.$on("sign-up", active => {
      this.active = active;
    });
  },
  computed: mapGetters({
    errors: "validation/errors"
  }),
  methods: {
    async register() {
      await this.$store.dispatch("auth/register", {
        name: this.name,
        email: this.email,
        password: this.password,
        password_confirmation: this.password_confirmation
      });

      await this.$store.dispatch("auth/login", {
        email: this.email,
        password: this.password
      });

      this.active = false;
      this.$store.dispatch("validation/clearErrors");
      this.$store.dispatch(
        "noti",
        {
          message: "Welcome, add products to your shopping cart!",
          type: "is-success"
        },
        { root: true }
      );
    },
    signIn() {
      this.active = false;
      EventBus.$emit("sign-in", true);
    }
  }
};
</script>