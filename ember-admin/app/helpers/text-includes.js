import { helper } from '@ember/component/helper';

export function textIncludes(params/*, hash*/) {
  return params[1].includes(params[0]);
}

export default helper(textIncludes);
