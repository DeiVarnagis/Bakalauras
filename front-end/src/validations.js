import { extend } from "vee-validate";

import { required, email, min, numeric, alpha } from "vee-validate/dist/rules";

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


extend("minNumber", {
  params: ['target'],
  validate(value, { target }) {
    return value >= target;
  },
  message: "Minimalus kiekis yra 1",
});