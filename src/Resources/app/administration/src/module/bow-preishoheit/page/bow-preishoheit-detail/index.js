import template from './bow-preishoheit-detail.html.twig';
const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bow-preishoheit-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
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

            this.productRepository.get(this.productId, Shopware.Context.api, criteria)
                .then((product) => {
                    this.product = product;
                    return Promise.all([
                        this.loadPriceHistory(),
                        this.loadErrorLogs()
                    ]);
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
            this.isSaveSuccessful = false;

            return this.productRepository.save(this.product, Shopware.Context.api)
                .then(() => {
                    this.isSaveSuccessful = true;
                    this.createNotificationSuccess({
                        title: this.$tc('global.default.success'),
                        message: this.$tc('global.notification.saveSuccess')
                    });
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: error.message || this.$tc('global.notification.saveError')
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
});
