import { Component, OnInit } from '@angular/core';
import {Router, ActivatedRoute, Params} from '@angular/router';
//import { User } from 'src/app/models/user';
import {User} from '../../../models/user';
import {UserService} from '../../../services/user.service';
import { ReCaptchaV3Service } from 'ngx-captcha';
import { SocialAuthService } from "angularx-social-login";
import { GoogleLoginProvider } from "angularx-social-login";
import { SocialUser } from "angularx-social-login";

@Component({
  selector: 'register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
  providers: [UserService]
})
export class RegisterComponent implements OnInit {
  public page_title: string;
  public user: User;
  public status: string;
  public token;
  public identity;
  public siteKey: string;
  public google_name:string;
  public captcha:string;
  public loading: boolean;

  public google_user: SocialUser;
  public loggedIn: boolean;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute,
    private reCaptchaV3Service: ReCaptchaV3Service,
    private authService: SocialAuthService,
  ) {
    this.page_title = 'Registrate'
    this.user=new User(1, '','','','','','');
    this.siteKey = '6Le-tuAaAAAAAGulKbnRuf1WJr2oFc30bbsjXvhv';
   }

  ngOnInit(){

  }

  onSubmit(form) {
this.loading=true;
    this._userService.register(this.user).subscribe(
      response => {

        if(response.status = "success"){
          this.status=response.status;
         // form.reset();
             //LOGIN
             this.reCaptchaV3Service.execute(this.siteKey, 'homepage', (captcha) => {
              this._userService.signup(this.user, captcha).subscribe(
                response => {
                  if (response.status != 'error') {
                    this.token = response.token;
                    this.identity = response.user;
                    this.captcha = captcha;
                    localStorage.setItem('token', this.token);
                    localStorage.setItem('identity', JSON.stringify(this.identity));
                    this._router.navigate(['home']);
                  } else {
                    this.status = 'error';
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
          
        }else{
          this.status = 'error';
        }
        
      },
      error=>{
        this.status = 'error';
        console.log(<any>error);
      }     
    ); 
  }


  signInWithGoogle(): void {
    this.authService.signIn(GoogleLoginProvider.PROVIDER_ID);
  }

  signOut(): void {
    this.authService.signOut();
  }

  googlelogin(){
    var resetForm = <HTMLFormElement>document.getElementById('registerform');
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
        localStorage.setItem('identity', JSON.stringify(this.identity));
        localStorage.setItem('origin', '{"googlelogin"}');
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
}
