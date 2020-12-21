import Home from "~/pages/home";
import Products from "~/pages/products";
import Cart from "~/pages/cart";
import Payment from "~/pages/payment";
import Bills from "~/pages/bills";

export default [
  { path: "/", name: "home", component: Products },
  { path: "/cart", name: "cart", component: Cart },
  { path: "/products/:type", name: "products", component: Products },
  { path: "/checkout/:id", name: "checkout", component: Payment },
  { path: "/bills-payment", name: "billsPayment", component: Bills }
];
