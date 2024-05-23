export const capitalizeFirstLetter = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
};

export const makeBreadcrumb = (text) => {
    return capitalizeFirstLetter(text.trim().replaceAll('-', ' '));
};

export const makeUrl = (text) => {
    return text
        .trim()
        .toLowerCase()
        .replaceAll(',', ' ')
        .replaceAll(' ', '-')
        .replaceAll('!', '');
};

export const setSlugCharMap = (slug) => {
    const chars = [
        ['ž', 'ž'],
        ['š', 'š'],
        ['č', 'č'],
        ['ć', 'ć'],
        ['đ', 'đ'],

        ['Ž', 'Ž'],
        ['Š', 'Š'],
        ['Č', 'Č'],
        ['Ć', 'Ć'],
        ['Đ', 'Đ'],

        ['(', '('],
        [')', ')'],
        ['/', ' '],
        [',', ' '],
    ];

    chars.forEach((charGroup) => {
        slug.charmap[charGroup[0]] = charGroup[1];
    });
};

export const shortenCarAcousticsAndElectronicsCategoryName = (category) => {
    if (category.id == '39597') {
        category.name = 'Akustika i elektronika';
    }
};

export const formatPrice = (number) => {
    return Number(number).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

export const formatNumber = (number) => {
    return Number(number).toLocaleString('de-DE', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });
};

export const session = {
    save: (key, data) => {
        sessionStorage.setItem(key, JSON.stringify(data));
    },
    load: (key) => {
        return JSON.parse(sessionStorage.getItem(key));
    },
};

export const getLastUrlPart = (url) => {
    return url
        .slice(1)
        .split('/')
        .map((entry) => decodeURIComponent(entry))
        .pop();
};
