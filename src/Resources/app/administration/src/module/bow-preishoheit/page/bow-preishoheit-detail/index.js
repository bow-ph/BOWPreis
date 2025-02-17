import template from './bow-preishoheit-detail.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    props: {
        productId: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            isLoading: false,
            isSaveSuccessful: false,
            product: null,
            priceHistory: [],
            errorLogs: []
        };
    },

    computed: {
        productRepository() {
            return this.repositoryFactory.create('bow_preishoheit_product');
        },

        priceHistoryRepository() {
            return this.repositoryFactory.create('bow_preishoheit_price_history');
        },

        errorLogRepository() {
            return this.repositoryFactory.create('bow_preishoheit_error_log');
        }
    },

    created() {
        this.loadData();
    },

    methods: {
        loadData() {
            this.isLoading = true;

            const criteria = new Criteria();
            criteria.addAssociation('product');

            return this.productRepository.get(this.productId, Shopware.Context.api, criteria)
                .then((product) => {
                    this.product = product;
                    return this.loadPriceHistory();
                })
                .then(() => {
                    return this.loadErrorLogs();
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        loadPriceHistory() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('productId', this.productId));
            criteria.addSorting(Criteria.sort('createdAt', 'DESC'));
            criteria.setLimit(50);

            return this.priceHistoryRepository.search(criteria, Shopware.Context.api)
                .then(({ items }) => {
                    this.priceHistory = items;
                });
        },

        loadErrorLogs() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('productId', this.productId));
            criteria.addSorting(Criteria.sort('createdAt', 'DESC'));
            criteria.setLimit(50);

            return this.errorLogRepository.search(criteria, Shopware.Context.api)
                .then(({ items }) => {
                    this.errorLogs = items;
                });
        },

        onSave() {
            this.isLoading = true;

            return this.productRepository.save(this.product, Shopware.Context.api)
                .then(() => {
                    this.isSaveSuccessful = true;
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
};
