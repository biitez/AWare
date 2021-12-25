# AWare — C# Ransomware

Ransomware with automatic Coinbase Commerce integration created in C# (Console) and PHP

PD: AWare is just a proof of concept, with this, you can read the encryption and see how it is used, and thus prevent a real one.

## About
Ransomware is a type of virus that prevents access to user files on their computer, encrypting them, until the user pays a ransom, in this case, $100, after payment, the program will automatically verify the status and decrypt the data of the user, to later close the process.

## How does it work
When the .EXE is opened, a request is sent to the PHP script, with a unique ID of the computer and the name, the server, creates a session, creates a password (with which the user's files will be encrypted) and a secret key with which it encrypts the password, sending it encrypted to the client, the program decrypts the encrypted password and encrypts the files on the computer, reading the bytes of the files and encrypting them, to later be saved with an .AWare extension, e.g, if you have a Image with the name cat.jpg, it will be encrypted and saved with the name cat.jpg.AWare, after that, you are redirected to a page with your session ID, the 'victim' clicks the 'Pay' button and a Coinbase order is generated, while the program sends requests to the server every 10 seconds looking for any payment made under that session, when the payment is completed, AWare will decrypt all the files with the '.AWare' extension and rename them, and your image cat.jpg.AWare, it will return to cat.jpg .

### Requirements
- PHP 7.0 or Higher
- Coinbase Commerce Account
- MySQL

### Usage
Create a database and import the db.sql file, then upload the PHP scripts to your server, you need to open the globals.php file and fill in the following definitions:

- DB_HOST (The address of your database, by default, localhost)
- DB_USER (The name of your user with privileged access to the database)
- DB_PASS (Your username password)
- DB_DATABASE (The name of your database)
- URL_PAGE (The link where you uploaded the panel.php)
- API_KEY_COINBASE_COMMERCE (The API-Key generated within your Coinbase Commerce account)
- SECRET_KEY_WEBHOOK_COINBASE_COMMERCE (Your webhook secret key, you can find it within your account)

Also, you must include the name of the 'webhook/index.php' within your coinbase commerce account, here I show you how to do it:

![WebhookAdd](https://share.biitez.dev/i/f80gi.png)
![WebhookUrl](https://share.biitez.dev/i/mda3v.png)
![WhereFindYourApis](https://share.biitez.dev/i/yqk22.png)

When you have done the above, you should open the project (.sln) and go to the globals.cs class, where you should place the API link (index.php) and the panel link (panel.php), then you just compile it and you can test it on a virtual machine.

### Screenshots / GIFs
![Screenshot1](https://share.biitez.dev/i/hye6l.png)

#### Files encrypted by AWare are renamed to .ex.AWare :
![FilesEncrypteds](https://share.biitez.dev/i/ce14q.png)

#### Encrypted txt example:
![PHPExampleTextEncrypted](https://share.biitez.dev/i/n762d.png)

#### Decrypted txt example:
![PHPExampleTextDecrypted](https://share.biitez.dev/i/dranl.png)

### Website :
![Website](https://share.biitez.dev/i/lzsw7.png)

#### When the payment reaches 1 confirmation through Coinbase Commerce:
![PayConfirmed](https://share.biitez.dev/i/gtwo7.gif)

# Note

This is a concept of a real ransomware operation, AWare is only created for educational purposes.

If you find any problem in the process, you can notify me, as well as if you want to improve the code or add something to it (I know you won't), you can do pull request.
