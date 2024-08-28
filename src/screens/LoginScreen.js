import { View, Text, Image, TextInput, TouchableOpacity } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import Image1 from "../../assets/images/image1.jpeg";
import { useState, React } from "react";
import { registeredUsers } from "../Constants";

const LoginScreen = ({ navigation }) => {
  const [uname, setUname] = useState("");
  const [passwd, setPasswd] = useState("");
  const [error, setError] = useState("");
  const [showPassword, setShowPassword] = useState(true);

  // This logic is to return that user whose username and password has been enterd
  const registeredUser = registeredUsers.find(
    (ruser) => ruser.username === uname && ruser.password === passwd
  );
  const handleLogin = () => {
    setUname(uname);
    setPasswd(passwd);
    if (uname === "" || passwd === "") {
      setError("Please Enter the Username and password");
    } else if (registeredUser) {
      navigation.navigate("FoodsList", { username: uname });
      setError("");
    } else {
      setError("Incorrect Username Or Password");
    }
    setUname("");
    setPasswd("");
  };

  return (
    <SafeAreaView style={{ flex: 1 }}>
      <View style={{ alignItems: "center", justifyContent: "center", flex: 1 }}>
        <Image
          source={Image1}
          style={{
            height: 250,
            width: 250,
            borderRadius: 150,
            marginBottom: 26,
          }}
        />
        <View>
          <Text style={{ fontSize: 20 }}>Enter Your Username and password</Text>

          <TextInput
            placeholder="Enter username"
            value={uname}
            onChangeText={setUname}
            style={{
              backgroundColor: "lightgray",
              color: "gray",
              paddingHorizontal: 16,
              paddingVertical: 16,
              borderRadius: 8,
              fontSize: 20,
              marginTop: 26,
              marginBottom: 16,
            }}
          />

          <View
            style={{
              backgroundColor: "lightgray",
              color: "gray",
              paddingHorizontal: 16,
              paddingVertical: 16,
              borderRadius: 8,

              marginBottom: 16,
              flexDirection: "row",
            }}
          >
            <TextInput
              placeholder="Enter password"
              value={passwd}
              onChangeText={setPasswd}
              secureTextEntry={showPassword}
              style={{ flex: 1, fontSize: 20 }}
            />

            <TouchableOpacity onPress={() => setShowPassword(!showPassword)}>
              <Text style={{ fontSize: 20, color: "green" }}>
                {passwd !== "" ? (showPassword ? "Show" : "Hide") : null}
              </Text>
            </TouchableOpacity>
          </View>

          {/* Displaying an error message when something goes wrong while logging in */}
          {error ? (
            <Text style={{ fontSize: 18, color: "red", marginBottom: 10 }}>
              {error}
            </Text>
          ) : null}

          <TouchableOpacity
            onPress={handleLogin}
            style={{
              backgroundColor: "#F96163",
              paddingHorizontal: 16,
              paddingVertical: 16,
              width: 320,
              borderRadius: 16,
              alignItems: "center",
            }}
          >
            <Text style={{ fontSize: 22, fontWeight: "600" }}>Login</Text>
          </TouchableOpacity>
        </View>
      </View>
    </SafeAreaView>
  );
};

export default LoginScreen;
