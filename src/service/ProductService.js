import axios from 'axios';

export default class ProductService {
    static apiResourceEndpoint = '/api/product';

    // get all related product data

    static async getDetails(id) {
        try {
            const pictures = await this.getProductPictures(id);
            const specifications =
                await this.getSpecificationAttributeForProduct(id);
            const carTypes = await axios.get(this.apiResourceEndpoint + '/getCarTypesForProduct/' + id)
            const oemCodes = await axios.get(this.apiResourceEndpoint + '/getOEMCodeForProduct/' + id)
 
            return {
                data: {
                    oemCodes: oemCodes.data,
                    carTypes: carTypes.data,
                    pictures: pictures.data,
                    specifications: specifications.data,
                },
            };
        } catch (error) {
            return { data: null, error };
        }
    }

    static async getProductById(id) {
        return axios.get(
            this.apiResourceEndpoint + '/getProductById/' + id,
        );
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
