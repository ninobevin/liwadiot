import Vue from "vue";
import VueRouter from "vue-router";

import Home from "../components/Home.vue";
import Devices from "../components/Devices.vue";
import Query from "../components/Query.vue";

Vue.use(VueRouter);

let routes = [
    { path: "/dash", component: Home },
    { path: "/devices", component: Devices },
    { path: "/query", component: Query },
    // {path: '*', component: NotFoundView}
];

export default new VueRouter({
    mode: "history",
    routes, // short for `routes: routes`
});