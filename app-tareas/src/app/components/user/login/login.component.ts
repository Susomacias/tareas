import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
//import { from } from 'rxjs';
import { User } from '../../../models/user';
import { UserService } from '../../../services/user.service';
import { ReCaptchaV3Service } from 'ngx-captcha';
import { SocialAuthService } from "angularx-social-login";
import { GoogleLoginProvider } from "angularx-social-login";
import { SocialUser } from "angularx-social-login";


@Component({
  selector: 'login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [UserService]
})
export class LoginComponent implements OnInit {
  public page_title: string;
  public user: User;
  public status: string;
  public origin: string;
  public token;
  public identity;
  public siteKey: string;
  public captcha:string;
  public google_name:string;
  public message:string;
  public loading:boolean; 

  public google_user: SocialUser;
  public loggedIn: boolean;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute,
    private reCaptchaV3Service: ReCaptchaV3Service,
    private authService: SocialAuthService,


  ) {
    this.page_title = 'Identificate';
    this.user = new User(1, '','','','','','');
    this.google_user = new SocialUser;
    this.siteKey = '6Le-tuAaAAAAAGulKbnRuf1WJr2oFc30bbsjXvhv';

    this.google_name = this.google_user.name;

  }

  ngOnInit() {
    //SE EJECUTA SIEMPRE Y CIERRA SESIÓN SOLO CUANDO LE LLEGA EL PARAMETRO SURE POR LA URL.
    this.logout();

    this.authService.authState.subscribe((user) => {
      this.google_user = user;
      this.loggedIn = (user != null);
    });
  }

  onSubmit(form) {
    this.loading=true
    this.reCaptchaV3Service.execute(this.siteKey, 'homepage', (captcha) => {
      //console.log('This is your token: ', captcha);
      this._userService.signup(this.user, captcha).subscribe(
        response => {
          if (response.status != 'error') {
            this.token = response.token;
            this.identity = response.user;
            this.captcha = captcha;
            localStorage.setItem('token', this.token);
            localStorage.setItem('identity', JSON.stringify(this.identity));
            localStorage.setItem('origin', '{"userlogin"}');
            this._router.navigate(['home']);
          } else {
            this.status = 'error';
            this.message= response.message;
          }
        },
        error => {
          this.status = 'error';
          console.log(<any>error);
        }
      );
    }, {
      useGlobalDomain: false
    });
  }

  signInWithGoogle(): void {
    this.authService.signIn(GoogleLoginProvider.PROVIDER_ID);
  }

  signOut(): void {
    this.authService.signOut();
  }

  googlelogin(){
    var resetForm = <HTMLFormElement>document.getElementById('loginform');
    resetForm.reset();
  this.authService.authState.subscribe((user) => {
      this.google_user = user;
      this.loggedIn = (user != null);
    
  this._userService.googlesignup(this.google_user).subscribe(
    response => {
      if(response.status ='success'){
        this.google_user=response.google_user;
        this.status= 'success';

        this.token = response.token;
        this.identity = response.user;

        localStorage.setItem('token', this.token);
        localStorage.setItem('origin', '{"googlelogin"}');
        localStorage.setItem('identity', JSON.stringify(this.identity));
        this._router.navigate(['home']);

      }else{
        this.status = 'error';
      }
    },
    error => {
      this.status = 'error';
      console.log(<any>error);
    }
  )
});
}

  logout() {
    localStorage.removeItem('budguet-detail');
    this._route.params.subscribe(params => {
      let logout = params['sure'];

      if (logout == 1) {
        localStorage.removeItem('identity');
        localStorage.removeItem('token');
        
        this.identity = null;
        this.token = null;

        //REDIRECCIÓN A INICIO
        this._router.navigate(['home']);
      } else {
        this.token = localStorage.getItem('token');
        this.identity = localStorage.getItem('identity');
        if (this.token != null &&
          this.identity != null) {
          this._router.navigate(['home']);
        }
      }
    })
  }
}

