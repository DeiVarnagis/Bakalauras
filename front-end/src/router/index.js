import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import NotFound from '../views/NotFound.vue'
import Registration from '../views/Registration.vue'
import Messages from '../views/Messages.vue';
import Email from '../views/EmailVerification.vue';
import EmailResend from '../views/EmailResend.vue';
import PasswordReset from '../views/PasswordReset.vue';
import Profile from '../views/Profile.vue';
import DeviceView from '../views/DeviceView';
import UsersTable from '../views/UsersTable';

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: {requiresAuth:true}
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta:{hideForAuth: true}
  },
  {
    path: '/register',
    name: 'Register',
    component: Registration,
    meta:{hideForAuth: true}
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: {requiresAuth:true}
  },
  {
    path: '/profile/:id',
    name: 'Profile',
    component: Profile,
    meta: {requiresAuth:true}
  },
  {
    path: '/messages',
    name: 'Messages',
    component: Messages,
    meta: {requiresAuth:true}
  },
  {
    path: '/device/:type/:id',
    name: 'DeviceView',
    component: DeviceView,
    meta: {requiresAuth:true}
  },
  {
    path: '/email/verify/:id',
    name: 'Email',
    component: Email,
  },
  {
    path: '/usersTable',
    name: 'usersTable',
    component: UsersTable,
  },
  {
    props: { title: 'Pamiršai slaptažodį? ', method:'password' },
    path: '/password/forgot/',
    name: 'passwordForgot',
    component: EmailResend,
  },
  {
    path: '/password/reset/:id',
    name: 'passwordReset',
    component: PasswordReset,
  },
  {
    path: '*',
    component: NotFound
  }
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

router.beforeEach((to,from,next) =>{
  if(to.matched.some(record => record.meta.requiresAuth)){
    if(!localStorage["token"]){
      next({
        name:'Login'
      });
    }else
    {
      next();
    }
  }else{
    next();
  }

  if (to.matched.some(record => record.meta.hideForAuth)) {
    if (localStorage["token"]) {
        next({ path: '/' });
    } else {
        next();
    }
} else {
    next();
}

})

export default router
