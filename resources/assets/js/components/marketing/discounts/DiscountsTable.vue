<script>
    import Flatify from '../../../classes/Flatify'

    export default {
        data() {
            return {
                loaded: false,
                discounts: [],
                checkedCount: 0,
                params: {
                    per_page: 15,
                    current_page: 1
                }
            }
        },
        mounted() {
            this.loadDiscounts();
            CandyEvent.$on('discount-added', product => {
                this.loadDiscounts();
            });
        },
        methods: {
            loadDiscount: function (id) {
                location.href = route('hub.discounts.edit', id);
            },
            status(discount) {
                if (!discount.status) {
                    return 'status-disabled'
                }
                return 'status-live';
            },
            loadDiscounts() {
                this.loaded = false;
                apiRequest.send('GET', 'discounts', [], this.params)
                    .then(response => {
                        this.discounts = response.data;
                        this.params.total_pages = response.meta.pagination.total_pages;
                        this.loaded = true;
                    });
            },
            getAttributeGroups(discount) {
                let groups = discount.attribute_groups.data,
                    visible = [];
                groups.forEach(group => {
                    visible.push(group.name);
                });
                if (!visible.length) {
                    return 'None';
                }
                return visible.join(', ');
            },
            changePage(page) {
                this.loaded = false;
                this.params.current_page = page;
                this.loadDiscounts();
            }
        }
    }
</script>

<template>
    <div>
        <!-- Search tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active" >
                <a href="#all-discounts" aria-controls="all-discounts" role="tab" data-toggle="tab">
                    All Discounts
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content section block">
            <div role="tabpanel" class="tab-pane active" id="all-discounts">
                <table class="table table-striped discount-table">
                    <thead>
                        <tr>
                            <th width="25%">Name</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Priority</th>
                            <th>Stop rules</th>
                            <th>Uses</th>
                        </tr>
                    </thead>
                    <tbody v-if="loaded">
                        <tr class="clickable" v-for="discount in discounts" @click="loadDiscount(discount.id)">
                            <td>
                                <figure class="status-icon">
                                    <span class="status" :class="status(discount)"></span>
                                </figure>
                                {{ discount|attribute('name') }}
                            </td>
                            <td>{{ discount.start_at|formatDate }}</td>
                            <td>{{ discount.end_at|formatDate }}</td>
                            <td>{{ discount.priority }}</td>
                            <td>{{ discount.stop_rules }}</td>
                            <td>{{ discount.uses }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="text-center" v-else>
                        <tr>
                            <td colspan="25" style="padding:40px;">
                                <div class="loading">
                                    <span><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></span> <strong>Loading</strong>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody v-if="!discounts.length">
                        <tr>
                            <td colspan="25" class="text-center">
                                <span class="text-info">You haven't created any discounts</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center" v-if="loaded">
                    <candy-table-paginate :pagination="params" @change="changePage"></candy-table-paginate>
                </div>
            </div>
        </div>
    </div>
</template>