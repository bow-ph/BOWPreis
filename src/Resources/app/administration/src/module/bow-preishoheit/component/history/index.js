import template from './history.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bow-preishoheit-history', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification')
    ],

    props: {
        product: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            history: [],
            isLoading: false,
            page: 1,
            limit: 25,
            total: 0,
            sortBy: 'createdAt',
            sortDirection: 'DESC'
        };
    },

    computed: {
        priceHistoryRepository() {
            return this.repositoryFactory.create('bow_preishoheit_price_history');
        },

        columns() {
            return [
                {
                    property: 'ean',
                    label: this.$tc('bow-preishoheit.history.columnEan'),
                    primary: true
                },
                {
                    property: 'oldPrice',
                    label: this.$tc('bow-preishoheit.history.columnOldPrice')
                },
                {
                    property: 'newPrice',
                    label: this.$tc('bow-preishoheit.history.columnNewPrice')
                },
                {
                    property: 'createdAt',
                    label: this.$tc('bow-preishoheit.history.columnDateTime')
                }
            ];
        }
    },

    data() {
        return {
            history: [],
            isLoading: false,
            page: 1,
            limit: 25,
            total: 0,
            sortBy: 'createdAt',
            sortDirection: 'DESC'
        };
    },

    created() {
        this.loadPriceHistory();
    },

    methods: {
        loadPriceHistory() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addFilter(Criteria.equals('productId', this.product.id));
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));

            return this.priceHistoryRepository.search(criteria, Shopware.Context.api)
                .then(result => {
                    this.history = result.items;
                    this.total = result.total;
                })
                .catch(error => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.history.errorTitle'),
                        message: error.message || this.$tc('bow-preishoheit.history.loadError')
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onPageChange({ page, limit }) {
            this.page = page;
            this.limit = limit;
            this.loadPriceHistory();
        },

        onSort({ sortBy, sortDirection }) {
            this.sortBy = sortBy;
            this.sortDirection = sortDirection;
            this.loadPriceHistory();
        },

        getPriceClass(item) {
            if (item.newPrice <= 0) return 'price--error';
            if (item.newPrice < item.oldPrice * 0.5) return 'price--warning';
            return '';
        }
    }
});
