import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PasswordRecoveriComponent } from './password-recoveri.component';

describe('PasswordRecoveriComponent', () => {
  let component: PasswordRecoveriComponent;
  let fixture: ComponentFixture<PasswordRecoveriComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PasswordRecoveriComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(PasswordRecoveriComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
