/*
 Copyright Siemens AG, 2017
 SPDX-License-Identifier:   GPL-2.0
 */

module.exports = class UploadSettings
{
    constructor(path, uploadName, description, uploadVisibility, agentSettings, licenseDeciderSettings, reuseSettings)
    {
        this._path = path;
        this._uploadName = uploadName;
        this._description = description;
        this._uploadVisibility = uploadVisibility;
        this._agentSettings = agentSettings;
        this._licenseDeciderSettings = licenseDeciderSettings;
        this._reuseSettings = reuseSettings;
    }

    get path() {
        return this._path;
    }

    get uploadName() {
        return this._uploadName;
    }

    get description() {
        return this._description;
    }

    get uploadVisibility() {
        return this._uploadVisibility;
    }

    get agentSettings() {
        return this._agentSettings;
    }

    get licenseDeciderSettings() {
        return this._licenseDeciderSettings;
    }

    get reuseSettings() {
        return this._reuseSettings;
    }

    set path(value) {
        this._path = value;
    }

    set uploadName(value) {
        this._uploadName = value;
    }

    set description(value) {
        this._description = value;
    }

    set uploadVisibility(value) {
        this._uploadVisibility = value;
    }

    set agentSettings(value) {
        this._agentSettings = value;
    }

    set licenseDeciderSettings(value) {
        this._licenseDeciderSettings = value;
    }

    set reuseSettings(value) {
        this._reuseSettings = value;
    }
}
