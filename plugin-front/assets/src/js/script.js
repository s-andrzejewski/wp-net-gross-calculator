/**
 * SASS
 */
import '../sass/layout.scss';

/**
 * JavaScript
 */
import axios from 'axios';

const calculator = document.querySelector('.ngc-calculator')
const form = calculator.querySelector('.ngc-calculator__form')
const elements = {
    'productName': form.querySelector('input[name="product_name"]'),
    'netAmount': form.querySelector('input[name="net_amount"]'),
    'currency': form.querySelector('input[name="currency"]'),
    'vatRate': form.querySelector('select[name="vat_rate"]'),
}

const calculate = async () => {
    // TODO: Make loading effect while waiting for response
    // TODO: Show banner in wordpress (error message (red) or success message (green))

    const oldResultElement = calculator.querySelector('.ngc-calculator__result')
    if (oldResultElement) {
        oldResultElement.remove()
    }

    try {
        const data = getValidData(form)
        const response = await axios.post('/wp-json/net-gross-calc/v1/calculate', data)
        console.log(response.data.message, response.data.result)
        fillFormWithResponse(response.data.result)
    }
    catch (error) {
        console.error(error);
        console.error(`Error while calculating (${error['status']}):`, error.response?.data?.message ? error.response.data.message : error.message)
    }
};

const handleForm = () => {
    const submitBtn = form.querySelector('.ngc-calculator__button')
    submitBtn.addEventListener('click', calculate)
}

const fillFormWithResponse = (result) => {
    const newResultElement = document.createElement('div')
    newResultElement.classList.add('ngc-calculator__result')

    const resultTextElement = document.createElement('p')
    resultTextElement.classList.add('ngc-calculator__result-text')
    resultTextElement.textContent = `The price of product "${elements.productName.value}", is: ${result.grossAmount} ${elements.currency.value}, the VAT amount is ${result.vatAmount} ${elements.currency.value}.`
    
    newResultElement.appendChild(resultTextElement);
    calculator.appendChild(newResultElement);
}

const getValidData = () => {
    const dataToValidate = {
        'productName': elements.productName.value,
        'netAmount': elements.netAmount.value,
        'currency': elements.currency.value,
        'vatRate': elements.vatRate.value,
    }

    const validationResult = validateCalculationFormData(dataToValidate);

    if (validationResult.valid) {
        console.log('Validated data:', validationResult.data);
    } else {
        console.error('Validation errors:', validationResult.errors);
    }

    return validationResult.data;
}

/**
 * Helper function to validate and format numbers.
 * 
 * This function takes an input string, validates it as a number, and formats it according to specified rules:
 * - Removes all whitespace characters.
 * - Replaces any commas (`,`) with dots (`.`) to ensure the number is properly formatted.
 * - Ensures that the number has two decimal places, adding `.00` if necessary.
 * 
 * @param {string} value - The input value to be validated and formatted.
 * @param {string} fieldName - The name of the field being validated. This is used for error messages.
 * @param {Array} [errors=[]] - An array to collect any validation error messages. The default is an empty array.
 * 
 * @returns {string|null} - Returns the formatted number as a string if valid, otherwise returns `null` and logs an error message in the `errors` array.
 * 
 * @throws {Error} - If the `errors` parameter is not an array.
 * 
 * @example
 * const errors = [];
 * const formattedNumber = validateAndFormatNumber(' 1234,56 ', 'Net Amount', errors);
 * if (formattedNumber) {
 *     console.log('Formatted Number:', formattedNumber); // Output: '1234.56'
 * } else {
 *     console.error('Errors:', errors);
 * }
 */
const validateAndFormatNumber = (value, fieldName, errors = []) => {
    // Ensure 'errors' is an array
    if (!Array.isArray(errors)) {
        console.error('"errors" must be an array');
        return;
    }

    if (typeof value !== 'string' || value.trim() === '') {
        errors.push(`${fieldName} must be a non-empty string.`);
        return null;
    }

    // Remove all whitespace chars
    value = value.replace(/\s+/g, '');

    value = value.replace(',', '.');

    // Convert the string to a float and check if it's a valid number
    let numberValue = parseFloat(value);
    if (isNaN(numberValue)) {
        errors.push(`${fieldName} must be a valid number.`);
        return null;
    }

    // Ensure the number has '.00' if it's a whole number
    if (!value.includes('.')) {
        value += '.00';
    } else {
        // If there's a dot, ensure there are exactly two decimal places
        let parts = value.split('.');
        if (parts[1].length === 1) {
            value += '0';
        }
        if (parts[1].length > 2) {
            // If there are more than two decimal places, round to twho digits after dot
            value = parseFloat(value).toFixed(2);
        }
    }

    return value;
};

const validateCalculationFormData = (data) => {
    const errors = [];

    //* productName
    if (typeof data.productName !== 'string' || data.productName.trim() === '') {
        errors.push('Product name must be a non-empty string.');
    }

    //* netAmount
    data.netAmount = validateAndFormatNumber(data.netAmount, 'Net amount', errors);

    //* currency
    if (typeof data.currency !== 'string' || data.currency.trim() === '') {
        errors.push('Currency must be a non-empty string.');
    }

    //* vatRate
    data.vatRate = validateAndFormatNumber(data.vatRate, 'VAT rate', errors);

    if (errors.length > 0) {
        return {
            valid: false,
            errors: errors
        };
    }

    return {
        valid: true,
        data: data
    };
}

form ? handleForm() : ''
