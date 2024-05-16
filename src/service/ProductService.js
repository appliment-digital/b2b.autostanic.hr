import axios from 'axios';

export default class ProductService {
    static apiResourceEndpoint = '/api/product';

    // get all related product data

    static async getDetails(productID) {
        const pictures = await this.getProductPictures(productID);
        const specifications =
            await this.getSpecificationAttributeForProduct(productID);

        console.log({ pictures, oemCodes, specifications, relatedVehicles });
        return { pictures, oemCodes, specifications, relatedVehicles };
    }

    // get

    static async getProductPictures(id) {
        return axios.get(
            this.apiResourceEndpoint + '/getProductPictures/' + id,
        );
    }

    static async getOEMCodeForProduct(id) {
        return axios.get(
            this.apiResourceEndpoint + '/getOEMCodeForProduct/' + id,
        );
    }

    static async getSpecificationAttributeForProduct(id) {
        return axios.get(
            this.apiResourceEndpoint +
                '/getSpecificationAttributeForProduct/' +
                id,
        );
    }

    static async getCarTypesForProduct(id) {
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
