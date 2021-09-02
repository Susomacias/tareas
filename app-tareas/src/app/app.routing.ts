//IMPORTS NECESARIOS
import { Route } from '@angular/compiler/src/core';
import {ModuleWithProviders} from '@angular/core';
import {Routes, RouterModule} from '@angular/router';
import { IdentityGuard} from './services/identity.guard';
import { from } from 'rxjs';

//IMPORTS COMPONENTES
import {HomeComponent} from './components/home/home.component';

import {ContactComponent} from './components/user/contact/contact.component';
import {CookieLawComponent} from './components/user/cookie-law/cookie-law.component';
import {UserEditComponent} from './components/user/user-edit/user-edit.component';
import {LoginComponent} from './components/user/login/login.component';
import {RegisterComponent} from './components/user/register/register.component';
import {ErrorComponent} from './components/user/error/error.component';
import {ChangePasswordComponent} from './components/user/change-password/change-password.component';
import {PasswordRecoveriComponent} from './components/user/password-recoveri/password-recoveri.component';


//DEFINIR LAS RUTAS
const appRoutes: Routes = [
    {path: '', component:HomeComponent},
    {path: 'home', component:HomeComponent},

    //USUARIO
    {path: 'contacto', component:ContactComponent},
    {path: 'politica-de-privacidad', component:CookieLawComponent},
    {path: 'login', component: LoginComponent},
    {path: 'logout/:sure', component: LoginComponent},
    {path: 'registro', component: RegisterComponent},
    {path: 'ajustes', component:UserEditComponent,canActivate: [IdentityGuard]},
    {path: 'password', component: ChangePasswordComponent,canActivate: [IdentityGuard]},
    {path: 'passwordrecoveri', component: PasswordRecoveriComponent},
    
    


   // canActivate: [IdentityGuard]
    
    
    /*EJEMPLO PADRE E HIJO
    {path: ':year/:id', component: YearDetailComponent,
    children: [
        {path: 'meses/:id', component: MesDetailComponent},
    ]},*/
   {path: '**', component: ErrorComponent}, //esta tiene que estar de ultima!!
];

//EXPORTAR CONFIGURACION
export const appRoutingProviders: any[] = [];
export const routing: ModuleWithProviders<Route> = RouterModule.forRoot(appRoutes);
