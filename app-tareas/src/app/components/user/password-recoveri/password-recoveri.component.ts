import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../../../models/user';
import { UserService } from '../../../services/user.service';

@Component({
  selector: 'app-password-recoveri',
  templateUrl: './password-recoveri.component.html',
  styleUrls: ['./password-recoveri.component.css'],
  providers: [UserService]
})
export class PasswordRecoveriComponent implements OnInit {
  public page_title: string;
  public user: User;
  public status: string;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute,
  ) { 
    this.page_title = 'Recuperar contraseña';
    this.user = new User(1, '','','','','','','','',1,'','');
  }

  ngOnInit(): void {
  }
  onSubmit(form) {
      this._userService.passwordrecoveri(this.user).subscribe(
        response => {
          if (response.status != 'error') {
            this.status='success'
          } else {
            this.status = 'error';
          }
        },
        error => {
          this.status = 'error';
          console.log(<any>error);
        }
      );
    }
}
