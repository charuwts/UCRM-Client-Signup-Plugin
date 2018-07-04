import { helper } from '@ember/component/helper';

export function periodText(params) {
  let type = 'Month';
  let amount = params[0];

  if (params[0] > 11) {
    type = 'Year';
    amount = params[0] / 12;
  }
  
  return amount + ' ' + type + ' period'; // Is String
}

export default helper(periodText);
