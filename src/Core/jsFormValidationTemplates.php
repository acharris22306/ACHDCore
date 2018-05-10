<?php require_once("initializations.php");  redirectTo(SITE_ROOT);?>
  
  <script type="text/javascript">
    
var name = new LiveValidation( "name", { validMessage: "√" } );
  name.add( Validate.Presence, {failureMessage: "Required"}  );
  name.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"} );

  var firstName = new LiveValidation( "firstName", { validMessage: "√" } );
  firstName.add( Validate.Presence, {failureMessage: "Required"}  );
  firstName.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"} );

  
  var lastName = new LiveValidation( "lastName", { validMessage: "√" } );
  lastName.add( Validate.Presence, {failureMessage: "Required"}  );
  lastName.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );

  
  var email = new LiveValidation( "email", { validMessage: "√" } );
  email.add( Validate.Presence, {failureMessage: "Required"});
  email.add( Validate.Length, { maximum: 50, tooLongMessage: "Must be less than 50 characters long" } );
  email.add( Validate.Email, {failureMessage: "Invalid Email"} );

  
  var email = new LiveValidation( "email", { validMessage: "√" } );
  email.add( Validate.Presence, {failureMessage: "Required"});
  email.add( Validate.Length, { maximum: 50, tooLongMessage: "Must be less than 50 characters long" } );
  email.add( Validate.Email, {failureMessage: "Invalid Email"} );

  
  var secondaryEmail = new LiveValidation( "secondaryEmail", { validMessage: "√" } );
  secondaryEmail.add( Validate.Length, { maximum: 50, tooLongMessage: "Must be less than 50 characters long" } );
  secondaryEmail.add( Validate.Email, {failureMessage: "Invalid Email"} );

  
  var subject = new LiveValidation( "subject", { validMessage: "√" } );
  subject.add( Validate.Presence , {failureMessage: "Required"} );
  subject.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
  var phone = new LiveValidation( "phone", { validMessage: "√" } );
  phone.add( Validate.Presence , {failureMessage: "Required"} );
  phone.add( Validate.Format, { pattern: /^((\(\d{3}\))|(\d{3}))([- ]?)\d{3}([- ]?)\d{4}$/, failureMessage: "Must be in (xxx)-xxx-xxxx or xxxxxxxxxx or xxx xxx xxxx or xxx-xxx-xxxx format" } );

  var phone = new LiveValidation( "phone", { validMessage: "√" } );
  phone.add( Validate.Presence , {failureMessage: "Required"} );
  phone.add( Validate.Format, { pattern: /^((\(\d{3}\))|(\d{3}))([- ]?)\d{3}([- ]?)\d{4}$/, failureMessage: "Must be in (xxx)-xxx-xxxx or xxxxxxxxxx or xxx xxx xxxx or xxx-xxx-xxxx format" } );

  var secondaryPhone = new LiveValidation( "secondaryPhone", { validMessage: "√" } );
  secondaryPhone.add( Validate.Format, { pattern: /^((\(\d{3}\))|(\d{3}))([- ]?)\d{3}([- ]?)\d{4}$/, failureMessage: "Must be in (xxx)-xxx-xxxx or xxxxxxxxxx or xxx xxx xxxx or xxx-xxx-xxxx format" } );

  
  var message = new LiveValidation( "message", { validMessage: "√" } );
  message.add( Validate.Presence , {failureMessage: "Required"} );
  
    
  var bio = new LiveValidation( "bio", { validMessage: "√" } );
  bio.add( Validate.Presence , {failureMessage: "Required"} );
  
    
  var referenceOne = new LiveValidation( "referenceOne", { validMessage: "√" } );
  referenceOne.add( Validate.Presence , {failureMessage: "Required"} );
  
    
  var referenceTwo = new LiveValidation( "referenceTwo", { validMessage: "√" } );
  referenceTwo.add( Validate.Presence , {failureMessage: "Required"} );
  
    
  var skillsInterests = new LiveValidation( "skillsInterests", { validMessage: "√" } );
  skillsInterests.add( Validate.Presence , {failureMessage: "Required"} );
  
     
  var otherEducationInfo = new LiveValidation( "otherEducationInfo", { validMessage: "√" } );
  otherEducationInfo.add( Validate.Presence , {failureMessage: "Required"} );
  
  
  
  var company = new LiveValidation( "company", { validMessage: "√" } );
  company.add( Validate.Presence , {failureMessage: "Required"} );
  company.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
    
  var address = new LiveValidation( "address", { validMessage: "√" } );
  address.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
    
  var googleHangoutsLink = new LiveValidation( "googleHangoutsLink", { validMessage: "√" } );
  googleHangoutsLink.add( Validate.Presence , {failureMessage: "Required"} );
  googleHangoutsLink.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
    
  var city = new LiveValidation( "city", { validMessage: "√" } );
  city.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
    
  var curatorDegree = new LiveValidation( "curatorDegree", { validMessage: "√" } );
  curatorDegree.add( Validate.Presence , {failureMessage: "Required"} );
  curatorDegree.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
    
  var curatorUniversity = new LiveValidation( "curatorUniversity", { validMessage: "√" } );
  curatorUniversity.add( Validate.Presence , {failureMessage: "Required"} );
  curatorUniversity.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
      
  var collegeUniversity = new LiveValidation( "collegeUniversity", { validMessage: "√" } );
  collegeUniversity.add( Validate.Presence , {failureMessage: "Required"} );
  collegeUniversity.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
        
  var college = new LiveValidation( "college", { validMessage: "√" } );
  college.add( Validate.Presence , {failureMessage: "Required"} );
  college.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
        
  var major = new LiveValidation( "major", { validMessage: "√" } );
  major.add( Validate.Presence , {failureMessage: "Required"} );
  major.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
           
  var degree = new LiveValidation( "degree", { validMessage: "√" } );
  degree.add( Validate.Presence , {failureMessage: "Required"} );
  degree.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
      
  var role = new LiveValidation( "role", { validMessage: "√" } );
  role.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
            
  var subject = new LiveValidation( "subject", { validMessage: "√" } );
  subject.add( Validate.Presence , {failureMessage: "Required"} );
  subject.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  
                 
  var state = new LiveValidation( "state", { validMessage: "√" } );
  state.add( Validate.Presence , {failureMessage: "Required"} );
                  
  var curatorState = new LiveValidation( "curatorState", { validMessage: "√" } );
  curatorState.add( Validate.Presence , {failureMessage: "Required"} );
  
                      
  var previouslyWorked = new LiveValidation( "previouslyWorked", { validMessage: "√" } );
  previouslyWorked.add( Validate.Presence , {failureMessage: "Required"} );
                  
  var whyUnfused = new LiveValidation( "whyUnfused", { validMessage: "√" } );
  whyUnfused.add( Validate.Presence , {failureMessage: "Required"} );
  
                         
  var dateOfBirth = new LiveValidation( "dateOfBirth", { validMessage: "√" } );
  dateOfBirth.add( Validate.Presence , {failureMessage: "Required"} );
                               
  var phone = new LiveValidation( "phone", { validMessage: "√" } );
  phone.add( Validate.Presence , {failureMessage: "Required"} );
                  
  var GPA = new LiveValidation( "GPA", { validMessage: "√" } );
  GPA.add( Validate.Presence , {failureMessage: "Required"} );
                    
  var userTypeSelect = new LiveValidation( "userTypeSelect", { validMessage: "√" } );
  userTypeSelect.add( Validate.Presence , {failureMessage: "Required"} );
  
      
  var topic = new LiveValidation( "topic", { validMessage: "√" } );
  topic.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
      
  var curatorAddress = new LiveValidation( "curatorAddress", { validMessage: "√" } );
  curatorAddress.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
      
  var curatorCity = new LiveValidation( "curatorCity", { validMessage: "√" } );
  curatorCity.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
      
  var title = new LiveValidation( "title", { validMessage: "√" } );
  title.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
                       
  var dateOfSession = new LiveValidation( "dateOfSession", { validMessage: "√" } );
  dateOfSession.add( Validate.Presence , {failureMessage: "Required"} );
                  
  var phoneCarrier = new LiveValidation( "phoneCarrier", { validMessage: "√" } );
  phoneCarrier.add( Validate.Presence , {failureMessage: "Required"} );
  
      
	                           
  var curatoredDate = new LiveValidation( "curatoredDate", { validMessage: "√" } );
  curatoredDate.add( Validate.Presence , {failureMessage: "Required"} );
                  
  var subject = new LiveValidation( "subject", { validMessage: "√" } );
  subject.add( Validate.Presence , {failureMessage: "Required"} );
                             
  var usernameAuthorizer = new LiveValidation( "usernameAuthorizer", { validMessage: "√" } );
  usernameAuthorizer.add( Validate.Presence , {failureMessage: "Required"} );
                  
  var passwordAuthorizer = new LiveValidation( "passwordAuthorizer", { validMessage: "√" } );
  passwordAuthorizer.add( Validate.Presence , {failureMessage: "Required"} );
                    
  var password = new LiveValidation( "password", { validMessage: "√" } );
  password.add( Validate.Presence , {failureMessage: "Required"} );
  password.add( Validate.Length, { minimum: 8, tooShortMessage: "Must be at least 8 characters long"  } );
  password.add( Validate.Format, { pattern: /^\S{8,}$/, failureMessage: "Must contain at least 8 characters" } );
                           
  var confirmPassword = new LiveValidation( "confirmPassword", { validMessage: "√" } );
  confirmPassword.add( Validate.Presence , {failureMessage: "Required"} );
  confirmPassword.add( Validate.Length, { minimum: 8, tooShortMessage: "Must be at least 8 characters long"  } );
  confirmPassword.add( Validate.Format, { pattern: /^\S{8,}$/, failureMessage: "Must contain at least 8 characters" } );
  confirmPassword.add( Validate.Confirmation, { match: 'password' , failureMessage: "Must Match Password" } );

         
                     
  var Password = new LiveValidation( "Password", { validMessage: "√" } );
  Password.add( Validate.Presence , {failureMessage: "Required"} );
  Password.add( Validate.Length, { minimum: 8, tooShortMessage: "Must be at least 8 characters long"  } );
  Password.add( Validate.Format, { pattern: /^\S{8,}$/, failureMessage: "Must contain at least 8 characters" } );
                           
  var confirmPassword = new LiveValidation( "confirmPassword", { validMessage: "√" } );
  confirmPassword.add( Validate.Presence , {failureMessage: "Required"} );
  confirmPassword.add( Validate.Length, { minimum: 8, tooShortMessage: "Must be at least 8 characters long"  } );
  confirmPassword.add( Validate.Format, { pattern: /^\S{8,}$/, failureMessage: "Must contain at least 8 characters" } );
  confirmPassword.add( Validate.Confirmation, { match: 'Password' , failureMessage: "Must Match Password" } );

      
  var username = new LiveValidation( "username", { validMessage: "√" } );
  username.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  username.add( Validate.Presence , {failureMessage: "Required"} );

              
  var confirmUsername = new LiveValidation( "confirmUsername", { validMessage: "√" } );
  confirmUsername.add( Validate.Length, { maximum: 50 , tooLongMessage: "Must be less than 50 characters long"}  );
  confirmUsername.add( Validate.Presence , {failureMessage: "Required"} );
  confirmUsername.add( Validate.Confirmation, { match: 'username', failureMessage: "Must Match Username"  } );

      
  var description = new LiveValidation( "description", { validMessage: "√" } );
  description.add( Validate.Presence , {failureMessage: "Required"} );
  
  var difficulty = new LiveValidation( "difficulty", { validMessage: "√" } );
  difficulty.add( Validate.Presence , {failureMessage: "Required"} );
  
  var zip = new LiveValidation ("zip", { validMessage: "√" });
  zip.add( Validate.Presence , {failureMessage: "Required"} );
  zip.add( Validate.Numericality, { minimum: 10000, maximum: 99999, onlyInteger: true, 
  		notANumberMessage: "Numbers Only", notAnIntegerMessage: "Whole Numbers Only" , tooLowMessage: "Must be at least 10000", tooHighMessage: "Must be no more than 999999" } );
				
  var minutesRequired = new LiveValidation ("minutesRequired", { validMessage: "√" });
  minutesRequired.add( Validate.Presence , {failureMessage: "Required"} );
  minutesRequired.add( Validate.Numericality, { minimum: 0, maximum: 99999, onlyInteger: true, 
  		notANumberMessage: "Numbers Only", notAnIntegerMessage: "Whole Numbers Only" , tooLowMessage: "Must be at least 0", tooHighMessage: "Must be no more than 999999" } );
				
  var sessionsRequired = new LiveValidation ("sessionsRequired", { validMessage: "√" });
  sessionsRequired.add( Validate.Presence , {failureMessage: "Required"} );
  sessionsRequired.add( Validate.Numericality, { minimum: 0, maximum: 99999, onlyInteger: true, 
  		notANumberMessage: "Numbers Only", notAnIntegerMessage: "Whole Numbers Only" , tooLowMessage: "Must be at least 0", tooHighMessage: "Must be no more than 999999" } );
				
  var numWouldRecommend = new LiveValidation ("numWouldRecommend", { validMessage: "√" });
  numWouldRecommend.add( Validate.Presence , {failureMessage: "Required"} );
  numWouldRecommend.add( Validate.Numericality, { minimum: 0, maximum: 99999, onlyInteger: true, 
  		notANumberMessage: "Numbers Only", notAnIntegerMessage: "Whole Numbers Only" , tooLowMessage: "Must be at least 0", tooHighMessage: "Must be no more than 999999" } );
					
  var totalTime = new LiveValidation ("totalTime", { validMessage: "√" });
  totalTime.add( Validate.Presence , {failureMessage: "Required"} );
  totalTime.add( Validate.Numericality, { minimum: 0, maximum: 99999, onlyInteger: true, 
  		notANumberMessage: "Numbers Only", notAnIntegerMessage: "Whole Numbers Only" , tooLowMessage: "Must be at least 0", tooHighMessage: "Must be no more than 999999" } );
					
  var entryId = new LiveValidation ("entryId", { validMessage: "√" });
  entryId.add( Validate.Presence , {failureMessage: "Required"} );
  entryId.add( Validate.Numericality, { minimum: 0, maximum: 999999999, onlyInteger: true, 
  		notANumberMessage: "Numbers Only", notAnIntegerMessage: "Whole Numbers Only" , tooLowMessage: "Must be at least 0", tooHighMessage: "Must be no more than 999999" } );
				
  var rateCurator = new LiveValidation ("rateCurator", { validMessage: "√" });
  zip.add( Validate.Presence , {failureMessage: "Required"} );
 
  var curatorTranscript = new LiveValidation ("curatorTranscript", { validMessage: "√" });
  curatorTranscript.add( Validate.Presence , {failureMessage: "Required"} );   

  var resume = new LiveValidation ("resume", { validMessage: "√" });
  resume.add( Validate.Presence , {failureMessage: "Required"} );
   
  var imageLocation = new LiveValidation ("imageLocation", { validMessage: "√" });
  imageLocation.add( Validate.Presence , {failureMessage: "Required"} );  
   
  var thumbnailLocation = new LiveValidation ("thumbnailLocation", { validMessage: "√" });
  thumbnailLocation.add( Validate.Presence , {failureMessage: "Required"} );
  
  
  </script>