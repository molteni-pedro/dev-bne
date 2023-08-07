import React from 'react';
import PropTypes from 'prop-types';

import { withStyles } from '@material-ui/core';
import Checkbox from '@material-ui/core/Checkbox';
import Clear from '@material-ui/icons/Clear';

const ErrorCheckbox = withStyles((theme) => ({
  root: {
    color: theme.palette.error.main,
  },
  checked: {
    color: theme.palette.error.main + ' !important',
  },
}))(Checkbox);

const SuccessCheckbox = withStyles((theme) => ({
  root: {
    color: theme.palette.success.main,
  },
  checked: {
    color: theme.palette.success.main + ' !important',
  },
}))(Checkbox);

const CheckboxWithValidation = ({ optionId, value, correctValues, checked, ...props }) => {
  if (correctValues) {
    if (checked && correctValues.includes(value)) {
      return (
        <SuccessCheckbox
          value={value}
          checked
          {...props}
          data-checkbox-option={optionId}
          data-test={'anu-lms-checkbox-success'}
        />
      );
    }

    if (!checked && correctValues.includes(value)) {
      return (
        <ErrorCheckbox
          value={value}
          checked
          {...props}
          data-checkbox-option={optionId}
          data-test={'anu-lms-checkbox-correct-error'}
        />
      );
    }

    if (checked && !correctValues.includes(value)) {
      return (
        <ErrorCheckbox
          value={value}
          checked
          checkedIcon={<Clear />}
          {...props}
          data-checkbox-option={optionId}
          data-test={'anu-lms-checkbox-incorrect-error'}
        />
      );
    }

    if (!checked && !correctValues.includes(value)) {
      return (
        <Checkbox
          value={value}
          checked={checked}
          {...props}
          data-checkbox-option={optionId}
          data-test={'anu-lms-checkbox'}
        />
      );
    }
  }

  return (
    <Checkbox
      value={value}
      checked={checked}
      {...props}
      data-checkbox-option={optionId}
      data-test={'anu-lms-checkbox'}
    />
  );
};

CheckboxWithValidation.propTypes = {
  optionId: PropTypes.string,
  value: PropTypes.string,
  correctValues: PropTypes.array,
  checked: PropTypes.bool,
};

export default CheckboxWithValidation;
