// create a directive for price
const PriceFormatterDirective = {
    mounted(el, binding) {
        el.innerHTML = format(binding.value);
    },
    updated(el, binding) {
        el.innerHTML = format(binding.value);
    }
};

const format = (value) => {
    // if value is string, remove the formatting
    if (typeof value === 'string') {
        value = value.replace(/[^\d.-]/g, '');
    }


    const price = Number(value / 100).toFixed(2);
    //const formattedPrice = price.replace(/\d(?=(\d{3})+\.)/g, '$&.');
    const [integerPart, decimalPart] = price.split('.');
    const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return `${formattedIntegerPart},${decimalPart} €`;
}

export {PriceFormatterDirective};
