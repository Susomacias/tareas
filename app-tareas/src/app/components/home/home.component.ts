import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { from } from 'rxjs';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { global } from '../../services/global.service';
import { ThisReceiver } from '@angular/compiler';

@Component({
  selector: 'home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
  providers: [UserService]
})

export class HomeComponent implements OnInit {

  public page_title: string;
  public user: User;
  public startElement: boolean;
  public startArticle: boolean;
  public startDepartment: boolean;
  public startCategory: boolean;
  public identity;
  public token;
  public status: string;
  public id;
  public keyframe;


  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute,
    private rest: UserService,

  ) {
    this.token = this._userService.getToken();
    this.identity = this._userService.getIdentity();
    if (!this.user) {
      localStorage.setItem('cover', 'true');
    }
  }

  ngOnInit(): void {
    if (!this.user) {
      localStorage.setItem('cover', 'false');
    }

    this.loadUser();
  }

  loadUser() {
    this.user = this._userService.getIdentity();
  }

}








