import { StyleSheet, Text, TextInput, View } from "react-native";

import { FontAwesome } from "@expo/vector-icons";
import { React } from "react";

const SearchFilter = ({ icon, placeholder, query, setQuery }) => {
  return (
    <View style={styles.searchBar}>
      <FontAwesome name={icon} size={20} color={"#f96163"} />
      <TextInput
        value={query}
        onChangeText={setQuery}
        placeholder={placeholder}
        style={{ fontSize: 16, marginLeft: 8, color: "#808080" }}
      />
    </View>
  );
};

export default SearchFilter;

const styles = StyleSheet.create({
  searchBar: {
    backgroundColor: "#fff",
    flexDirection: "row",
    paddingHorizontal: 16,
    paddingVertical: 16,
    borderRadius: 8,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowRadius: 7,
    shadowOpacity: 0.1,
  },
});
