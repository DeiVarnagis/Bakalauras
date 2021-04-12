import { extend } from "vee-validate";

import { required, email, min, numeric, alpha, min_value, max_value} from "vee-validate/dist/rules";

extend("email", email);

extend("email", {
  ...email,
  message: "Laukas privalo būti email formato",
});

extend("required", {
  ...required,
  message: "Laukas privalomas",
});

extend("min", {
  params: ['target'],
  validate(value, { target }) {
    return value.length > target.length;
  },
  ...min,
  message: "Minimalus simbolių skaičius 8",
});

extend('password', {
  params: ['target'],
  validate(value, { target }) {
    return value === target;
  },
  message: 'Slaptažodžiai nesutampa'
});


extend("numeric", {
  ...numeric,
  message: "Laukas privalo būti skaičius",
});

extend("alpha", {
  ...alpha,
  message: "Laukas privalo būti sudarytas tik iš raidžių",
});

extend("maxNumber", {
  params: ['target'],
  validate(value, { target }) {
    return value <= target;
  },
  ...max_value,
  message: "Maksimalus kiekis yra  30",
});

extend("minNumber", {
  params: ['target'],
  validate(value, { target }) {
    return value >= target;
  },
  ...min_value,
  message: "Minimalus kiekis yra  1",
});
