import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import {FormsModule} from '@angular/forms';
//import {HttpClient, HttpHeaders} from '@angular/common/http';
import { HttpClientModule } from '@angular/common/http';
import {routing, appRoutingProviders} from './app.routing';
import { ReactiveFormsModule } from '@angular/forms';

import { NgxCaptchaModule } from 'ngx-captcha';
import { SocialLoginModule, SocialAuthServiceConfig } from 'angularx-social-login';
import { GoogleLoginProvider } from 'angularx-social-login'; //angularx-social-login


import { AppComponent } from './app.component';
import { HomeComponent } from './components/home/home.component';
import { AngularFileUploaderModule } from "angular-file-uploader";
import { ContactComponent } from './components/user/contact/contact.component';
import { CookieLawComponent } from './components/user/cookie-law/cookie-law.component';
import { LoginComponent } from './components/user/login/login.component';
import { RegisterComponent } from './components/user/register/register.component';
import { UserEditComponent } from './components/user/user-edit/user-edit.component';
import { UserService } from './services/user.service';
import { IdentityGuard} from './services/identity.guard';
import { ErrorComponent } from './components/user/error/error.component';
import { ChangePasswordComponent } from './components/user/change-password/change-password.component';
import { PasswordRecoveriComponent } from './components/user/password-recoveri/password-recoveri.component';

import {MatSlideToggleModule} from '@angular/material/slide-toggle';
import {AutocompleteLibModule} from 'angular-ng-autocomplete';
import {MatAutocompleteModule} from '@angular/material/autocomplete';
import {MatFormFieldModule} from '@angular/material/form-field';
import {MatInputModule} from '@angular/material/input';
import {MatExpansionModule} from '@angular/material/expansion';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import {MatProgressBarModule} from '@angular/material/progress-bar';
import {DragDropModule} from '@angular/cdk/drag-drop';

import { PdfMakeWrapper } from 'pdfmake-wrapper';
import * as pdfFonts from "pdfmake/build/vfs_fonts";// fonts provided for pdfmake


PdfMakeWrapper.setFonts(pdfFonts);


@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    ContactComponent,
    CookieLawComponent,
    LoginComponent,
    RegisterComponent,
    UserEditComponent,
    ErrorComponent,
    ChangePasswordComponent,
    PasswordRecoveriComponent,

  ],
  imports: [
    BrowserModule,
    routing,
    FormsModule,
    HttpClientModule,
    AngularFileUploaderModule,
    ReactiveFormsModule,
    NgxCaptchaModule,
    SocialLoginModule,
    MatSlideToggleModule,
    AutocompleteLibModule,
    MatAutocompleteModule,
    MatFormFieldModule,
    MatInputModule,
    MatExpansionModule,
    BrowserAnimationsModule,
    MatProgressBarModule,
    DragDropModule

  ],
  providers: [
    {provide: 'SocialAuthServiceConfig',
      useValue: { 
        autoLogin: false, providers: 
        [{id: GoogleLoginProvider.PROVIDER_ID,
          provider: new GoogleLoginProvider('396756697427-ha04mugspccpvt1epe2ov76g4okfnbao.apps.googleusercontent.com')}]
      } as SocialAuthServiceConfig,
    },

    appRoutingProviders,
    IdentityGuard,
    UserService,
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
