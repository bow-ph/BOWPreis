import template from './product-grid.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            isLoading: false,
            selectedProducts: [],
            availableProducts: [],
            columns: [{
                property: 'name',
                label: 'Name',
                primary: true
            }, {
                property: 'productNumber',
                label: 'Product Number'
            }, {
                property: 'ean',
                label: 'EAN'
            }]
        };
    },

    computed: {
        productRepository() {
            return this.repositoryFactory.create('product');
        },

        preishoheitProductRepository() {
            return this.repositoryFactory.create('bow_preishoheit_product');
        }
    },

    created() {
        this.loadProducts();
    },

    methods: {
        loadProducts() {
            this.isLoading = true;

            const criteria = new Criteria();
            criteria.addAssociation('preishoheitProduct');
            criteria.addFilter(Criteria.not('AND', [
                Criteria.equals('preishoheitProduct.id', null)
            ]));

            return this.productRepository.search(criteria)
                .then(({ items }) => {
                    this.availableProducts = items;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onSelectionChange(selection) {
            this.selectedProducts = Object.values(selection);
        },

        onAddProducts() {
            if (!this.selectedProducts.length) {
                return;
            }

            const newProducts = this.selectedProducts.map(product => {
                return {
                    id: Shopware.Utils.createId(),
                    productId: product.id,
                    active: true,
                    surchargePercentage: 0,
                    discountPercentage: 0
                };
            });

            this.isLoading = true;
            return this.preishoheitProductRepository.sync(newProducts)
                .then(() => {
                    this.createNotificationSuccess({
                        message: this.$tc('bow-preishoheit.list.messageSaveSuccess')
                    });
                    this.loadProducts();
                })
                .catch((error) => {
                    this.createNotificationError({
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
};
