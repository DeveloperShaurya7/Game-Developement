const { app, BrowserWindow } = require("electron");

function createWindow() {

  const win = new BrowserWindow({

    width: 1400,
    height: 900,

    minWidth: 1000,
    minHeight: 700,

    icon: "sudoku.ico",

    autoHideMenuBar: true,

    webPreferences: {

      nodeIntegration: true,
      contextIsolation: false
    }
  });

  win.loadFile("index.html");
}

app.whenReady().then(() => {

  createWindow();
});