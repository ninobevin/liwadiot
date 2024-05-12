<template>
    <div class="d-flex flex-column">
        <v-card>
            <v-card-text>
                <div>
                    <p>Database Query Browser</p>
                </div>

                <v-data-table
                    :headers="table_header"
                    :items="table_data"
                    :items-per-page="100"
                    class="elevation-1"
                    item-key="data_table"
                    v-model="selected_row"
                    dense
                >
                    <template v-slot:top>
                        <v-card-text>
                            <v-row dense>
                                <v-col class="col-sm-12 col-md-4">
                                    <v-select
                                        :items="device_types"
                                        v-model="selected_device_type"
                                        label="Device Type"
                                        dense
                                        solo
                                        @change="onSelectDeviceType"
                                    ></v-select>
                                </v-col>
                                <v-col class="col-sm-12 col-md-4">
                                    <v-select
                                        :items="devices"
                                        label="Device"
                                        dense
                                        solo
                                        v-model="selected_device"
                                        @change="onSelectDevice()"
                                    ></v-select>
                                </v-col>
                                <v-col class="col-sm-12 col-md-4">
                                    <v-select
                                        v-model="selected_attrib"
                                        :items="attribs"
                                        label="Attributes"
                                        multiple
                                        chips
                                        small-chips
                                        solo
                                    ></v-select>
                                </v-col>
                            </v-row>
                            <v-row dense>
                                <v-col class="col-sm-12 col-md-4">
                                    <v-dialog
                                        v-model="dialog_date_from"
                                        max-width="290"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                v-model="date_from"
                                                label="Select Date"
                                                readonly
                                                solo
                                                prepend-inner-icon="mdi-calendar"
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="date_from"
                                            title="Date from"
                                            scrollable
                                            @input="
                                                $refs.dialog.save(date_from)
                                            "
                                        ></v-date-picker>
                                    </v-dialog>
                                </v-col>
                                <v-col class="col-sm-12 col-md-4">
                                    <v-dialog
                                        v-model="dialog_date_to"
                                        max-width="290"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                v-model="date_to"
                                                label="Select Date"
                                                readonly
                                                solo
                                                prepend-inner-icon="mdi-calendar"
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="date_to"
                                            title="Date from"
                                            scrollable
                                            @input="$refs.dialog.save(date_to)"
                                        ></v-date-picker>
                                    </v-dialog>
                                </v-col>
                                <v-col class="col-sm-12 col-md-4"> </v-col>
                            </v-row>
                            <v-row dense>
                                <v-col class="col-sm-12 col-md-8">
                                    <v-btn
                                        style="text-transform: none"
                                        color="primary"
                                        alignSelf="center"
                                        :loading="page_loading"
                                        width="100%"
                                        @click="getTelemetry()"
                                    >
                                        GET TELEMETRY</v-btn
                                    >
                                </v-col>

                                <v-col class="col-sm-12 col-md-4">
                                    <v-btn
                                        v-if="table_data.length"
                                        width="100%"
                                    >
                                        <download-excel
                                            style="width: 100%"
                                            class="btn"
                                            :data="table_data"
                                        >
                                            Export
                                            <v-icon>mdi-file-excel</v-icon>
                                        </download-excel>
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
export default {
    mounted() {
        //this.getToken();
    },
    data() {
        return {
            date_from: null,
            dialog_date_from: false,
            date_to: null,
            dialog_date_to: false,

            page_loading: false,
            devices: [],
            selected_device: null,
            selected_device_type: null,
            attribs: [],
            selected_attrib: null,

            device_types: [
                { text: "DEVICE", value: "DEVICE" },
                { text: "ASSET", value: "ASSET" },
            ],
            keyword_search: null,
            token: null,
            table_data: [],
            table_header: [
                { text: "Date", value: "ts", sortable: false },
                { text: "Telemetry", value: "value", sortable: false },
            ],
            selected_row: {},
        };
    },
    methods: {
        getToken() {
            axios.get("/iot/getToken").then((r) => {
                this.token = r.data.token;
                console.log(this.token);
            });
        },
        getDevices() {
            this.page_loading = true;
            axios.get("/iot/getDevices").then((r) => {
                this.devices = r.data;
                // console.log(this.token);
                this.page_loading = false;
            });
        },
        onSelectDeviceType() {
            console.log(this.selected_device_type);

            if (this.selected_device_type == "DEVICE") {
                this.getDevices();
            }
        },
        getTelemetry() {
            this.page_loading = true;
            axios
                .get("/iot/getTelemetry", {
                    params: {
                        device_type: this.selected_device_type,
                        device_id: this.selected_device.id,
                        device_name: this.selected_device.name,
                        date_from: this.date_from,
                        date_to: this.date_to,
                        keys: this.selected_attrib,
                    },
                })
                .then((r) => {
                    console.log(r.data);

                    this.table_header = r.data.table_header;
                    this.table_data = r.data.table_data;

                    this.page_loading = false;
                })
                .catch((e) => {
                    this.page_loading = false;
                });
        },
        onSelectDevice() {
            this.page_loading = true;
            axios
                .get("/iot/getAttrib", {
                    params: {
                        device_id: this.selected_device.id,
                        device_type: this.selected_device_type,
                    },
                })
                .then((r) => {
                    //console.log(r.data);
                    this.attribs = r.data;
                    this.page_loading = false;
                });
        },
    },
};
</script>
