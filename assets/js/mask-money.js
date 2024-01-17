import SimpleMaskMoney from "simple-mask-money";

let args = {
    prefix: '',
    suffix: '',
    fixed: true,
    fractionDigits: 2,
    decimalSeparator: ',',
    thousandsSeparator: '.'
};

let input = SimpleMaskMoney.setMask('.price', args);
input.formatToNumber();