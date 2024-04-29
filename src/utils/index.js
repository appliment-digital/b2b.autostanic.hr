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
