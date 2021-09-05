import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import {AfterViewInit, ElementRef, ViewChild} from '@angular/core';
import { User } from '../../../models/user';
import { Password } from '../../../models/password';
import { UserService } from '../../../services/user.service';

@Component({
  selector: 'app-change-password',
  templateUrl: './change-password.component.html',
  styleUrls: ['./change-password.component.css'],
  providers: [UserService]
})
export class ChangePasswordComponent implements OnInit {
  public page_title: string;
  public user: User;
  public password: Password;
  public status: string;
  public origin: string;
  public token;
  public identity;
  public newpassword1:string;
  public newpassword2:string;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute
  ) {
    this.page_title = 'Contraseña';
    this.token = this._userService.getToken();
    this.user = new User(1, '','','','','','');
    this.password = new Password('');

    this.identity = this._userService.getIdentity();
    this.origin = this._userService.getOriginLogin();


    this.newpassword1 = this.user.newpassword;
    this.newpassword2 = this.password.newpassword;
  }

  ngOnInit(): void {
        //Si loguea con google redirije a Home.
        if (this.origin=='{"googlelogin"}'){this._router.navigate(['home']);}
  }

  onSubmit(form) {
    if (this.user.newpassword == this.password.newpassword) {
      this._userService.password(this.token, this.user).subscribe(
        response => {
          if (response && response.status) {
            this.status = 'success';
            form.reset();

          } else {
            this.status = 'error';
          }
        },
        error => {
          this.status = 'error';
          console.log(<any>error);
        }
      );
    } else {
      window.alert("La contraseña nueva no coincide con su confirmarcion");
    }
  }
}


