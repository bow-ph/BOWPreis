import template from './bulk-edit.html.twig';
import './bulk-edit.scss';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bow-preishoheit-bulk-edit', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            products: [],
            selectedItems: [],
            isLoading: false,
            isSaving: false,
            page: 1,
            limit: 25,
            total: 0,
            sortBy: 'name',
            sortDirection: 'ASC',
            bulkEditData: {
                adjustmentType: 'percentage',
                value: 0
            },
            columns: [
                { property: 'name', label: this.$tc('bow-preishoheit.bulk.columnName'), primary: true },
                { property: 'productNumber', label: this.$tc('bow-preishoheit.bulk.columnProductNumber') },
                { property: 'price', label: this.$tc('bow-preishoheit.bulk.columnPrice') },
                { property: 'preishoheitProduct.surchargePercentage', label: this.$tc('bow-preishoheit.bulk.columnSurcharge') }
            ],
            showBulkEditModal: false,
            isSaving: false,
            processingStatus: {
                total: 0,
                processed: 0,
                success: 0,
                failed: 0
            }
        };
    },

    inject: ['repositoryFactory'],

    mixins: [Mixin.getByName('notification')],

    computed: {
        productRepository() {
            return this.repositoryFactory.create('product');
        },

        preishoheitProductRepository() {
            return this.repositoryFactory.create('bow_preishoheit_product');
        },

        productCriteria() {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('preishoheitProduct');
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));
            return criteria;
        },

        isValidBulkEdit() {
            return this.bulkEditData.value !== null && this.bulkEditData.value !== '';
        }
    },

    created() {
        this.loadProducts();
    },

    methods: {
        loadProducts() {
            this.isLoading = true;

            return this.productRepository.search(this.productCriteria, Shopware.Context.api)
                .then(result => {
                    this.products = result.items;
                    this.total = result.total;
                })
                .catch(error => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.bulk.errorTitle'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onPageChange({ page, limit }) {
            this.page = page;
            this.limit = limit;
            this.loadProducts();
        },

        onSort({ sortBy, sortDirection }) {
            this.sortBy = sortBy;
            this.sortDirection = sortDirection;
            this.loadProducts();
        },

        onBulkEditClick() {
            this.showBulkEditModal = true;
        },

        onCloseBulkEditModal() {
            this.showBulkEditModal = false;
        },

        async onSaveBulkEdit() {
            if (!this.isValidBulkEdit || !this.selectedProducts.length) {
                return;
            }

            this.isSaving = true;
            this.processingStatus = {
                total: this.selectedProducts.length,
                processed: 0,
                success: 0,
                failed: 0
            };

            const requests = this.selectedProducts.map(async product => {
                try {
                    if (!product.preishoheitProduct) {
                        const newEntry = this.preishoheitProductRepository.create(Shopware.Context.api);
                        newEntry.productId = product.id;
                        newEntry.surchargePercentage = this.bulkEditData.value;
                        await this.preishoheitProductRepository.save(newEntry, Shopware.Context.api);
                    } else {
                        product.preishoheitProduct.surchargePercentage = this.bulkEditData.value;
                        await this.preishoheitProductRepository.save(product.preishoheitProduct, Shopware.Context.api);
                    }
                    this.processingStatus.success++;
                } catch {
                    this.processingStatus.failed++;
                } finally {
                    this.processingStatus.processed++;
                }
            });

            await Promise.all(productUpdates);

            if (this.processingStatus.failed > 0) {
                this.createNotificationWarning({
                    title: this.$tc('bow-preishoheit.bulk.partialSuccessTitle'),
                    message: this.$tc('bow-preishoheit.bulk.partialSuccessMessage', 0, { failed: this.processingStatus.failed })
                });
            } else {
                this.createNotificationSuccess({
                    title: this.$tc('bow-preishoheit.bulk.successTitle'),
                    message: this.$tc('bow-preishoheit.bulk.successMessage')
                });
            }

            this.onCloseBulkEditModal();
            this.loadProducts();
            this.isSaving = false;
        }
    }
});
