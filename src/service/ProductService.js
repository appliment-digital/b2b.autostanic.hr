import axios from 'axios';

export default class ProductService {
    static apiResourceEndpoint = '/api/product';

    // get

    static async getPictures(id) {
        return axios.get(
            this.apiResourceEndpoint + '/getProductPictures/' + id,
        );
    }

    static async getSpecifications(id) {
        return axios.get(
            this.apiResourceEndpoint +
                '/getSpecificationAttributeForProduct/' +
                id,
        );
    }

    static async getOEMCodes(id) {
        return axios.get(
            this.apiResourceEndpoint + '/getOEMCodeForProduct/' + id,
        );
    }


    static async getCarTypes(id) {
        return axios.get(
            this.apiResourceEndpoint + '/getCarTypesForProduct/' + id,
        );
    }

    static async getProductDetails(id) {
        return axios.get(this.apiResourceEndpoint + '/getProductDetails/' + id);
    }

    // post

    static async getProductsByCategoryId(
        categoryId,
        page,
        pageSize,
        filter = {},
    ) {
        return axios.post(
            this.apiResourceEndpoint +
                '/getProductsByCategoryId/' +
                categoryId +
                '/' +
                page +
                '/' +
                pageSize,
            filter,
        );
    }

    static async getProductsBySupplierCategoresAndPriceRange(object) {
        return axios.post(
            this.apiResourceEndpoint +
                '/getProductsBySupplierCategoresAndPriceRange',
            object,
        );
    }
}
