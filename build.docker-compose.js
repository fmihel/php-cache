/* eslint-disable no-promise-executor-return */
/* eslint-disable no-use-before-define */
/* eslint-disable array-callback-return */
/* eslint-disable camelcase */
const readline = require('readline');
const path = require('path');
const fs = require('fs');

const templateFile = './docker-compose.template';
const outFileName = 'docker-compose.yaml';

const vars = {
    DIR: __dirname,
    MYSQL_ROOT_PASSWORD: 'root',
    MYSQL_VOLUME: `${__dirname}/mysql`,
    NETWORK: 'docker_network',
    PHP_PORT: '8080',
    PHPADMIN_PORT: '8090',

    DDD(content) {
        return content.replaceAll('<%DDD%>', 'ddd');
    },
};

const readLine = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
});

// utils --------------------------------------------------------------------------
const question = (str) => new Promise((resolve) => readLine.question(str, resolve));
const questionWithDefault = async (str, defalt = false) => {
    const input = await question(str);
    return input.trim() === '' ? defalt : input;
};
const questionVar = async (msg, varName) => {
    vars[varName] = await questionWithDefault(`${msg}, default "${vars[varName]}": `, vars[varName]);
    return vars[varName];
};
const loadFile = (fileName) => new Promise((ok) => {
    fs.readFile(fileName, 'utf8', (e, data) => { ok(data); });
});
const saveFile = (fileName, text) => new Promise((ok) => {
    const dir = path.dirname(fileName);
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
    }
    fs.writeFile(fileName, text, 'utf8', () => { ok(); });
});
const existsPath = (path) => new Promise((ok)=>{
    fs.access(path, error => {
        ok(error?false:true)
      });
    });
// ---------------------------------------------------------------------------------

const dialogs = {
    start: async () => {
        console.log('------------------------------------');
        console.log('generate docker compose script:');
        return dialogs.loadTemplate();
    },
    loadTemplate: async () => {
        const template = await loadFile(templateFile);
        return dialogs.reDefineVars(template);
    },
    reDefineVars: async (template) => {
        const qv = async (msg, varName) => {
            if (template.indexOf(`<%${varName}%>`) !== -1) await questionVar(msg, varName);
            return 1;
        };

        await qv('set Mysql root password', 'MYSQL_ROOT_PASSWORD');
        await qv('set Mysql volume path', 'MYSQL_VOLUME');
        if (! (await existsPath(vars.MYSQL_VOLUME))){
            if (await questionWithDefault(`path "${vars.MYSQL_VOLUME}" not exists, create y(default)/n `,'y') ==='y'){
                fs.mkdirSync(vars.MYSQL_VOLUME, { recursive: true });
            }
        }
        await qv('set Docker network name', 'NETWORK');
        await qv('set Apache port with php', 'PHP_PORT');
        await qv('set PhpAdmin port', 'PHPADMIN_PORT');
        return dialogs.setVars(template);
    },
    setVars: async (template) => {
        let result = template;
        const keys = Object.keys(vars);
        keys.map((key) => {
            const value = vars[key];
            if (typeof value === 'function') {
                result = value(result);
            } else {
                result = result.replaceAll(`<%${key}%>`, value);
            }
        });
        dialogs.save(result);

        // dialogs.end();
    },
    save: async (template) => {
        await saveFile(outFileName, template);
        return dialogs.end();
    },
    end: async (msg = `generate "${outFileName}" ok.`) => {
        console.log(msg);
        readLine.close();
        console.log('------------------------------------');
    },
};

dialogs.start();
