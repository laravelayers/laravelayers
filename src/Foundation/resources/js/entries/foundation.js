// Configuring Plugins

import '../foundation.abide';

// Custom Plugins

import { FormBeforeunload } from '../formBeforeunload.js';
Foundation.plugin(FormBeforeunload, 'FormBeforeunload');

import { FormCheckbox } from '../formCheckbox';
Foundation.plugin(FormCheckbox, 'FormCheckbox');

import { FormDatetime } from '../formDatetime';
Foundation.plugin(FormDatetime, 'FormDatetime');

import { FormEditor } from '../formEditor';
Foundation.plugin(FormEditor, 'FormEditor');

import { FormFile } from '../formFile';
Foundation.plugin(FormFile, 'FormFile');

import { FormSearch } from '../formSearch';
Foundation.plugin(FormSearch, 'FormSearch');

import { FormSelect } from '../formSelect';
Foundation.plugin(FormSelect, 'FormSelect');
