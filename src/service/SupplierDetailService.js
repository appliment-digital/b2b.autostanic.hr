import Axios from 'axios';

export default class SupplierDetailService {
    apiResourceEndpoint = '/api/supplierDetail';

    async getSupplierWithDetails(id) {
        return Axios.get(
            this.apiResourceEndpoint + '/getSupplierWithDetails/' + id,
        );
    }

    async getCategoryName(id) {
        return Axios.get(this.apiResourceEndpoint + '/getCategoryName/' + id);
    }

    async getProductName(id) {
        return Axios.get(this.apiResourceEndpoint + '/getProductName/' + id);
    }

    async getAllSuppliersWithDetails() {
        return Axios.get(
            this.apiResourceEndpoint + '/getAllSuppliersWithDetails',
        );
    }

    async getUniqueCategories() {
        return Axios.get(this.apiResourceEndpoint + '/getUniqueCategories');
    }

    async getAddedPriceRange(object) {
        return Axios.post(
            this.apiResourceEndpoint + '/getAddedPriceRange',
            object,
        );
    }

    async getDetailsForProduct(object) {
        return Axios.post(
            this.apiResourceEndpoint + '/getDetailsForProduct',
            object,
        );
    }

    async addDetailsforSupplier(object) {
        return Axios.post(
            this.apiResourceEndpoint + '/addDetailsforSupplier',
            object,
        );
    }

    async updateDetailsforSupplier(id, object) {
        return Axios.put(
            this.apiResourceEndpoint + '/updateDetailsforSupplier/' + id,
            object,
        );
    }
}
