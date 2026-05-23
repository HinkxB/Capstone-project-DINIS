package main

import (
	"encoding/json"
	"fmt"
	"log"

	"github.com/hyperledger/fabric-contract-api-go/contractapi"
)

type SmartContract struct {
	contractapi.Contract
}

// --- STATE STRUCTS ---
type CitizenRecord struct {
	DocType    string `json:"docType"`
	PersonId   string `json:"personId"`
	NrcNumber  string `json:"nrcNumber"`
	FatherNrc  string `json:"fatherNrc"`
	MotherNrc  string `json:"motherNrc"`
	RecordHash string `json:"recordHash"`
}

type MarriageRecord struct {
	DocType         string `json:"docType"`
	MarriageId      string `json:"marriageId"`
	WifePersonId    string `json:"wifePersonId"`
	HusbandPersonId string `json:"husbandPersonId"`
	RecordHash      string `json:"recordHash"`
}

type SecondaryIdentity struct {
	DocType    string `json:"docType"`
	ProfileId  int    `json:"profileId"`
	PersonId   string `json:"personId"`
	SystemName string `json:"systemName"`
	RecordHash string `json:"recordHash"`
}

// --- EVENT STRUCTS ---
type CitizenAnchoredEvent struct {
	PersonId   string `json:"personId"`
	NrcNumber  string `json:"nrcNumber"`
	RecordHash string `json:"recordHash"`
}

type MarriageAnchoredEvent struct {
	MarriageId string `json:"marriageId"`
	RecordHash string `json:"recordHash"`
}

type SecondaryIdentityAnchoredEvent struct {
	ProfileId int    `json:"profileId"`
	PersonId  string `json:"personId"`
}

type VerifyResult struct {
	IsAnchored bool   `json:"isAnchored"`
	RecordHash string `json:"recordHash"`
}

// --- SMART CONTRACT METHODS ---

func (s *SmartContract) AnchorCitizenRecord(ctx contractapi.TransactionContextInterface, personId string, nrcNumber string, fatherNrc string, motherNrc string, recordHash string) (bool, error) {
	
	// 1. Prevent Overwriting Existing NRC
	citizenKey := "NRC_" + nrcNumber
	existing, _ := ctx.GetStub().GetState(citizenKey)
	if existing != nil {
		return false, fmt.Errorf("NRC %s is already anchored on-chain", nrcNumber)
	}

	// 2. LINEAGE VERIFICATION: Ensure parents exist on-chain (if provided)
	if fatherNrc != "" {
		fatherBytes, _ := ctx.GetStub().GetState("NRC_" + fatherNrc)
		if fatherBytes == nil {
			return false, fmt.Errorf("lineage error: Father NRC %s does not exist on-chain", fatherNrc)
		}
	}
	
	if motherNrc != "" {
		motherBytes, _ := ctx.GetStub().GetState("NRC_" + motherNrc)
		if motherBytes == nil {
			return false, fmt.Errorf("lineage error: Mother NRC %s does not exist on-chain", motherNrc)
		}
	}

	// 3. Save Record
	citizen := CitizenRecord{
		DocType:    "citizen",
		PersonId:   personId,
		NrcNumber:  nrcNumber,
		FatherNrc:  fatherNrc,
		MotherNrc:  motherNrc,
		RecordHash: recordHash,
	}

	citizenJSON, _ := json.Marshal(citizen)
	err := ctx.GetStub().PutState(citizenKey, citizenJSON)
	if err != nil {
		return false, fmt.Errorf("failed to put citizen state: %v", err)
	}

	// 4. Emit Event
	eventPayload := CitizenAnchoredEvent{
		PersonId:   personId,
		NrcNumber:  nrcNumber,
		RecordHash: recordHash,
	}
	eventJSON, _ := json.Marshal(eventPayload)
	ctx.GetStub().SetEvent("CitizenAnchored", eventJSON)

	return true, nil
}

func (s *SmartContract) AnchorMarriageEvent(ctx contractapi.TransactionContextInterface, marriageId string, wifePersonId string, husbandPersonId string, recordHash string) (bool, error) {
	marriageKey := "MARRIAGE_" + marriageId
	marriage := MarriageRecord{
		DocType:         "marriage",
		MarriageId:      marriageId,
		WifePersonId:    wifePersonId,
		HusbandPersonId: husbandPersonId,
		RecordHash:      recordHash,
	}

	marriageJSON, _ := json.Marshal(marriage)
	ctx.GetStub().PutState(marriageKey, marriageJSON)

	eventPayload := MarriageAnchoredEvent{
		MarriageId: marriageId,
		RecordHash: recordHash,
	}
	eventJSON, _ := json.Marshal(eventPayload)
	ctx.GetStub().SetEvent("MarriageAnchored", eventJSON)

	return true, nil
}

func (s *SmartContract) AnchorSecondaryIdentity(ctx contractapi.TransactionContextInterface, profileId int, personId string, systemName string, recordHash string) (bool, error) {
	profileKey := fmt.Sprintf("SEC_ID_%d", profileId)
	profile := SecondaryIdentity{
		DocType:    "secondary_identity",
		ProfileId:  profileId,
		PersonId:   personId,
		SystemName: systemName,
		RecordHash: recordHash,
	}

	profileJSON, _ := json.Marshal(profile)
	ctx.GetStub().PutState(profileKey, profileJSON)

	eventPayload := SecondaryIdentityAnchoredEvent{
		ProfileId: profileId,
		PersonId:  personId,
	}
	eventJSON, _ := json.Marshal(eventPayload)
	ctx.GetStub().SetEvent("SecondaryIdentityAnchored", eventJSON)

	return true, nil
}

func (s *SmartContract) VerifyAnchor(ctx contractapi.TransactionContextInterface, nrcNumber string) (*VerifyResult, error) {
	citizenKey := "NRC_" + nrcNumber
	citizenBytes, err := ctx.GetStub().GetState(citizenKey)
	if err != nil {
		return nil, fmt.Errorf("failed to read state: %v", err)
	}

	if citizenBytes == nil {
		return &VerifyResult{IsAnchored: false, RecordHash: ""}, nil
	}

	var citizen CitizenRecord
	json.Unmarshal(citizenBytes, &citizen)

	return &VerifyResult{
		IsAnchored: true,
		RecordHash: citizen.RecordHash,
	}, nil
}

func main() {
	chaincode, err := contractapi.NewChaincode(&SmartContract{})
	if err != nil {
		log.Panicf("Error creating chaincode: %v", err)
	}
	if err := chaincode.Start(); err != nil {
		log.Panicf("Error starting chaincode: %v", err)
	}
}
