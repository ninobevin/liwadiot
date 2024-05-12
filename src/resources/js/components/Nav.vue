<template>
    <v-card>
        <v-navigation-drawer
            app
            v-model="drawer"
            :mini-variant.sync="mini"
            permanent
        >
            <v-list-item>
                <v-list-item-avatar>
                    <v-img
                        src="https://randomuser.me/api/portraits/men/85.jpg"
                    ></v-img>
                </v-list-item-avatar>

                <v-list-item-title>{{ user.name }} tet</v-list-item-title>

                <v-btn icon @click.stop="mini = !mini">
                    <v-icon>chevron_left</v-icon>
                </v-btn>
            </v-list-item>

            <v-divider></v-divider>

            <v-list dense>
                <v-list-item
                    v-for="item in items"
                    :key="item.title"
                    :to="item.link"
                    link
                >
                    <v-list-item-icon>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
    </v-card>
</template>
<script>
export default {
    mounted() {
        this.user = window.user;
    },
    data() {
        return {
            user: {},
            drawer: true,
            items: [
                { title: "Home", icon: "mdi-home-city" },
                {
                    title: "Devices",
                    icon: "mdi-cellphone-link",
                    link: "/devices",
                },
                {
                    title: "Query",
                    icon: "mdi-database",
                    link: "/query",
                },
                { title: "Users", icon: "mdi-account-group-outline" },
            ],
            mini: false,
        };
    },
};
</script>
