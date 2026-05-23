'use strict';

const { Contract } = require('fabric-contract-api');

class IdentityContract extends Contract {

    async RegisterCitizen(ctx, nrcNumber, fullName, dob, sex, placeOfBirth, district, village, chief, motherName, motherNrc, fatherName, fatherNrc, registrationDate, biometricHash) {
        const identity = {
            nrcNumber,
            fullName,
            dob,
            sex,
            placeOfBirth,
            district,
            village,
            chief,
            motherName,
            motherNrc,
            fatherName,
            fatherNrc,
            registrationDate,
            biometricHash,
            assetType: 'citizen'
        };

        // Convert the object to a string buffer to store on the ledger
        await ctx.stub.putState(nrcNumber, Buffer.from(JSON.stringify(identity)));
        return JSON.stringify(identity);
    }

    async ReadIdentity(ctx, nrcNumber) {
        const identityJSON = await ctx.stub.getState(nrcNumber);
        if (!identityJSON || identityJSON.length === 0) {
            throw new Error(`The identity ${nrcNumber} does not exist`);
        }
        return identityJSON.toString();
    }
}

module.exports = IdentityContract;
