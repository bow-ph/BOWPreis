import template from './history.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    props: {
        product: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            isLoading: false,
            priceHistory: [],
            total: 0,
            page: 1,
            limit: 25
        };
    },

    computed: {
        priceHistoryRepository() {
            return this.repositoryFactory.create('bow_preishoheit_price_history');
        },

        priceHistoryColumns() {
            return [{
                property: 'ean',
                label: this.$tc('bow-preishoheit.detail.columnEan'),
                primary: true
            }, {
                property: 'oldPrice',
                label: this.$tc('bow-preishoheit.detail.columnOldPrice')
            }, {
                property: 'newPrice',
                label: this.$tc('bow-preishoheit.detail.columnNewPrice')
            }, {
                property: 'createdAt',
                label: this.$tc('bow-preishoheit.detail.columnDate')
            }];
        }
    },

    created() {
        this.loadPriceHistory();
    },

    methods: {
        loadPriceHistory() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addFilter(Criteria.equals('productId', this.product.id));
            criteria.addSorting(Criteria.sort('createdAt', 'DESC'));

            return this.priceHistoryRepository.search(criteria)
                .then(({ items, total }) => {
                    this.priceHistory = items;
                    this.total = total;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onPageChange({ page, limit }) {
            this.page = page;
            this.limit = limit;
            this.loadPriceHistory();
        }
    }
};
